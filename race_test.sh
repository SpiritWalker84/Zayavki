#!/bin/bash

# Скрипт для проверки защиты от race condition при взятии заявки в работу
# Использование: ./race_test.sh <BASE_URL> <REQUEST_ID> <MASTER_TOKEN>
# Пример: ./race_test.sh http://localhost:8080 1 "your_csrf_token"

BASE_URL="${1:-http://localhost:8080}"
REQUEST_ID="${2:-1}"
CSRF_TOKEN="${3}"

if [ -z "$CSRF_TOKEN" ]; then
    echo "Ошибка: Необходимо указать CSRF токен"
    echo "Использование: $0 <BASE_URL> <REQUEST_ID> <CSRF_TOKEN>"
    echo ""
    echo "Как получить CSRF токен:"
    echo "1. Войдите в систему как мастер"
    echo "2. Откройте консоль браузера и выполните:"
    echo "   document.querySelector('meta[name=\"csrf-token\"]').content"
    exit 1
fi

echo "Тестирование race condition для заявки #$REQUEST_ID"
echo "URL: $BASE_URL"
echo ""

# Функция для отправки запроса
send_request() {
    local request_num=$1
    echo "[Запрос $request_num] Отправка запроса на взятие заявки в работу..."
    
    response=$(curl -s -w "\n%{http_code}" \
        -X POST \
        -H "Content-Type: application/x-www-form-urlencoded" \
        -H "X-CSRF-TOKEN: $CSRF_TOKEN" \
        -H "Cookie: $(curl -s -c /tmp/cookies.txt -b /tmp/cookies.txt $BASE_URL/login | grep -o 'laravel_session=[^;]*' | head -1)" \
        -d "_token=$CSRF_TOKEN" \
        "$BASE_URL/master/requests/$REQUEST_ID/take" 2>&1)
    
    http_code=$(echo "$response" | tail -n1)
    body=$(echo "$response" | sed '$d')
    
    echo "[Запрос $request_num] HTTP код: $http_code"
    
    if [ "$http_code" -eq 200 ] || [ "$http_code" -eq 302 ]; then
        echo "[Запрос $request_num] ✓ Успешно"
        return 0
    else
        echo "[Запрос $request_num] ✗ Ошибка или конфликт"
        return 1
    fi
}

# Отправляем два параллельных запроса
echo "Отправка двух параллельных запросов..."
echo ""

send_request 1 &
PID1=$!

send_request 2 &
PID2=$!

# Ждем завершения обоих запросов
wait $PID1
RESULT1=$?

wait $PID2
RESULT2=$?

echo ""
echo "Результаты:"
echo "  Запрос 1: $([ $RESULT1 -eq 0 ] && echo 'Успешно' || echo 'Ошибка/Конфликт')"
echo "  Запрос 2: $([ $RESULT2 -eq 0 ] && echo 'Успешно' || echo 'Ошибка/Конфликт')"
echo ""

# Проверяем результат
if [ $RESULT1 -eq 0 ] && [ $RESULT2 -eq 0 ]; then
    echo "⚠ ВНИМАНИЕ: Оба запроса были успешными! Возможна проблема с защитой от race condition."
    exit 1
elif [ $RESULT1 -eq 0 ] || [ $RESULT2 -eq 0 ]; then
    echo "✓ Тест пройден: Только один запрос был успешным, второй получил ошибку/конфликт"
    exit 0
else
    echo "⚠ Оба запроса завершились с ошибкой. Проверьте правильность параметров."
    exit 1
fi
