#!/bin/bash

# =================================================================
# Practical Rate Limiting Test - Test langsung ke server
# =================================================================

echo "🧪 RATE LIMITING PRACTICAL TEST"
echo "================================"
echo ""

BASE_URL="${1:-http://localhost:8000}"

echo "Testing against: $BASE_URL"
echo ""

# =================================================================
# Test 1: Register Rate Limit (5/minute)
# =================================================================
echo "📝 Test 1: Register Endpoint (Limit: 5/min)"
echo "-------------------------------------------"

SUCCESS=0
RATE_LIMITED=0

for i in {1..7}; do
    RESPONSE=$(curl -s -w "\n%{http_code}" -X POST "$BASE_URL/api/auth/register" \
        -H "Content-Type: application/json" \
        -H "Accept: application/json" \
        -d "{
            \"username\": \"testuser$i$RANDOM\",
            \"phone_number\": \"0812345678$i$RANDOM\",
            \"password\": \"Password123\"
        }")

    HTTP_CODE=$(echo "$RESPONSE" | tail -n1)

    if [ "$HTTP_CODE" = "429" ]; then
        echo "  Request $i: ❌ Rate Limited (HTTP $HTTP_CODE) - EXPECTED!"
        ((RATE_LIMITED++))
    else
        echo "  Request $i: ✅ Allowed (HTTP $HTTP_CODE)"
        ((SUCCESS++))
    fi

    # Small delay
    sleep 0.1
done

if [ $RATE_LIMITED -gt 0 ]; then
    echo "✅ PASS: Rate limiting active after $SUCCESS successful requests"
else
    echo "❌ FAIL: No rate limiting detected"
fi

echo ""

# =================================================================
# Test 2: Login Rate Limit (10/minute)
# =================================================================
echo "🔐 Test 2: Login Endpoint (Limit: 10/min)"
echo "------------------------------------------"

SUCCESS=0
RATE_LIMITED=0

for i in {1..12}; do
    RESPONSE=$(curl -s -w "\n%{http_code}" -X POST "$BASE_URL/api/auth/login" \
        -H "Content-Type: application/json" \
        -H "Accept: application/json" \
        -d '{
            "phone_number": "08123456789",
            "password": "wrongpassword"
        }')

    HTTP_CODE=$(echo "$RESPONSE" | tail -n1)

    if [ "$HTTP_CODE" = "429" ]; then
        echo "  Request $i: ❌ Rate Limited (HTTP $HTTP_CODE) - EXPECTED!"
        ((RATE_LIMITED++))
    else
        echo "  Request $i: ✅ Allowed (HTTP $HTTP_CODE)"
        ((SUCCESS++))
    fi

    sleep 0.1
done

if [ $RATE_LIMITED -gt 0 ]; then
    echo "✅ PASS: Rate limiting active after $SUCCESS successful requests"
else
    echo "❌ FAIL: No rate limiting detected"
fi

echo ""

# =================================================================
# Test 3: Password Reset Rate Limit (5/minute)
# =================================================================
echo "🔑 Test 3: Password Reset (Limit: 5/min)"
echo "-----------------------------------------"

SUCCESS=0
RATE_LIMITED=0

for i in {1..7}; do
    RESPONSE=$(curl -s -w "\n%{http_code}" -X POST "$BASE_URL/api/forgot-password" \
        -H "Content-Type: application/json" \
        -H "Accept: application/json" \
        -d '{
            "phone_number": "08123456789"
        }')

    HTTP_CODE=$(echo "$RESPONSE" | tail -n1)

    if [ "$HTTP_CODE" = "429" ]; then
        echo "  Request $i: ❌ Rate Limited (HTTP $HTTP_CODE) - EXPECTED!"
        ((RATE_LIMITED++))
    else
        echo "  Request $i: ✅ Allowed (HTTP $HTTP_CODE)"
        ((SUCCESS++))
    fi

    sleep 0.1
done

if [ $RATE_LIMITED -gt 0 ]; then
    echo "✅ PASS: Rate limiting active after $SUCCESS successful requests"
else
    echo "❌ FAIL: No rate limiting detected"
fi

echo ""
echo "================================"
echo "✅ Rate Limiting Tests Complete"
echo "================================"
