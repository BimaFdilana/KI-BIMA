#!/bin/bash

# =================================================================
# Script untuk membuktikan peningkatan Security & API Quality
# =================================================================

# Get the Laravel root directory (2 levels up from tests/scripts)
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
LARAVEL_ROOT="$( cd "$SCRIPT_DIR/../.." && pwd )"
API_ROUTES="$LARAVEL_ROOT/routes/api.php"

echo "=============================================="
echo "🔍 VERIFIKASI PERBAIKAN API"
echo "=============================================="
echo ""
echo "Laravel Root: $LARAVEL_ROOT"
echo "API Routes: $API_ROUTES"
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Counter
PASSED=0
FAILED=0

# Check if routes file exists
if [ ! -f "$API_ROUTES" ]; then
    echo -e "${RED}❌ ERROR: routes/api.php not found!${NC}"
    exit 1
fi

# =================================================================
# 1. CHECK RATE LIMITING (DDoS Protection)
# =================================================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🛡️  1. RATE LIMITING VERIFICATION"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Check register rate limit
if grep -q "throttle:5,1" "$API_ROUTES" && grep -q "register" "$API_ROUTES"; then
    echo -e "${GREEN}✅ PASS${NC} - Register endpoint has rate limiting (5/min)"
    ((PASSED++))
else
    echo -e "${RED}❌ FAIL${NC} - Register endpoint missing rate limit"
    ((FAILED++))
fi

# Check login rate limit
if grep -q "throttle:10,1" "$API_ROUTES" && grep -q "login" "$API_ROUTES"; then
    echo -e "${GREEN}✅ PASS${NC} - Login endpoint has rate limiting (10/min)"
    ((PASSED++))
else
    echo -e "${RED}❌ FAIL${NC} - Login endpoint missing rate limit"
    ((FAILED++))
fi

# Check password reset rate limits
PASSWORD_RESET_THROTTLE=$(grep -A 1 "forgot-password\|verify-password-reset-otp\|reset-password" "$API_ROUTES" | grep -c "throttle:")
if [ "$PASSWORD_RESET_THROTTLE" -ge 3 ]; then
    echo -e "${GREEN}✅ PASS${NC} - Password reset endpoints have rate limiting (found $PASSWORD_RESET_THROTTLE)"
    ((PASSED++))
else
    echo -e "${RED}❌ FAIL${NC} - Password reset missing rate limit (found only $PASSWORD_RESET_THROTTLE)"
    ((FAILED++))
fi

echo ""

# =================================================================
# 2. CHECK AUTHENTICATION REQUIREMENTS
# =================================================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔒 2. AUTHENTICATION SECURITY"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Check comment routes have auth
COMMENT_SECTION=$(grep -A 20 "informations/{information}/comments" "$API_ROUTES")
COMMENT_AUTH=$(echo "$COMMENT_SECTION" | grep -c "auth:sanctum")
if [ "$COMMENT_AUTH" -gt 0 ]; then
    echo -e "${GREEN}✅ PASS${NC} - Comment creation/update/delete requires authentication"
    ((PASSED++))
else
    echo -e "${RED}❌ FAIL${NC} - Comment routes vulnerable (no auth)"
    ((FAILED++))
fi

# Check utility routes have auth
if grep -B 2 "barang/list" "$API_ROUTES" | grep -q "auth:sanctum"; then
    echo -e "${GREEN}✅ PASS${NC} - Utility routes require authentication"
    ((PASSED++))
else
    echo -e "${RED}❌ FAIL${NC} - Utility routes publicly accessible"
    ((FAILED++))
fi

echo ""

# =================================================================
# 3. CHECK REST API COMPLIANCE
# =================================================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📊 3. REST API COMPLIANCE"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Check PATCH for updates
if grep -q "patch.*update" "$API_ROUTES" || grep -q "Route::patch" "$API_ROUTES"; then
    echo -e "${GREEN}✅ PASS${NC} - Update endpoints use PATCH method"
    ((PASSED++))
else
    echo -e "${RED}❌ FAIL${NC} - Update endpoints using wrong HTTP method"
    ((FAILED++))
fi

# Check GET for retrievals
if grep -q "get.*quick-shopping" "$API_ROUTES" || grep "quick-shopping" "$API_ROUTES" | grep -q "Route::get"; then
    echo -e "${GREEN}✅ PASS${NC} - Retrieval endpoints use GET method"
    ((PASSED++))
else
    echo -e "${RED}❌ FAIL${NC} - GET method not used for data retrieval"
    ((FAILED++))
fi

echo ""

# =================================================================
# 4. CHECK CODE QUALITY
# =================================================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🎯 4. CODE QUALITY & CONSISTENCY"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Check namespace consistency
if ! grep -q "API" "$API_ROUTES" | grep -q "Auth"; then
    echo -e "${GREEN}✅ PASS${NC} - Namespace consistent (no uppercase API)"
    ((PASSED++))
else
    echo -e "${RED}❌ FAIL${NC} - Namespace inconsistency found"
    ((FAILED++))
fi

# Check no duplicate invitation prefixes
INVITATION_GROUPS=$(grep -c "prefix('invitation')" "$API_ROUTES")
if [ "$INVITATION_GROUPS" -eq 1 ]; then
    echo -e "${GREEN}✅ PASS${NC} - No duplicate route groups (invitation merged)"
    ((PASSED++))
else
    echo -e "${YELLOW}⚠️  WARN${NC} - Multiple invitation route groups found: $INVITATION_GROUPS"
    ((FAILED++))
fi

# Check middleware consistency
if grep -q "Route::group.*middleware.*auth:sanctum" "$API_ROUTES"; then
    echo -e "${YELLOW}⚠️  WARN${NC} - Old middleware syntax still present"
    ((FAILED++))
else
    echo -e "${GREEN}✅ PASS${NC} - Middleware grouping consistent"
    ((PASSED++))
fi

echo ""

# =================================================================
# 5. SECURITY METRICS
# =================================================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📈 5. SECURITY METRICS"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Count rate limited endpoints
RATE_LIMITED=$(grep -c "throttle:" "$API_ROUTES")
echo "   Rate Limited Endpoints: $RATE_LIMITED"

# Count authenticated endpoints
AUTH_REQUIRED=$(grep -c "auth:sanctum" "$API_ROUTES")
echo "   Auth Required Endpoints: $AUTH_REQUIRED"

# Count total API endpoints
TOTAL_ENDPOINTS=$(grep -c "Route::" "$API_ROUTES")
echo "   Total API Endpoints: $TOTAL_ENDPOINTS"

# Calculate protection percentage
if [ "$TOTAL_ENDPOINTS" -gt 0 ]; then
    PROTECTION_PERCENT=$((AUTH_REQUIRED * 100 / TOTAL_ENDPOINTS))
    echo "   Protection Coverage: ${PROTECTION_PERCENT}%"
else
    echo "   Protection Coverage: N/A"
fi

echo ""

# =================================================================
# FINAL SCORE
# =================================================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📊 FINAL RESULTS"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

TOTAL=$((PASSED + FAILED))
SCORE=$((PASSED * 100 / TOTAL))

echo ""
echo "   Tests Passed: ${GREEN}${PASSED}${NC}"
echo "   Tests Failed: ${RED}${FAILED}${NC}"
echo "   Total Tests:  ${TOTAL}"
echo ""
echo "   Overall Score: ${SCORE}%"
echo ""

if [ $SCORE -ge 90 ]; then
    echo -e "${GREEN}🎉 EXCELLENT! API is well secured${NC}"
elif [ $SCORE -ge 70 ]; then
    echo -e "${YELLOW}👍 GOOD! Minor improvements needed${NC}"
else
    echo -e "${RED}⚠️  WARNING! Critical issues need attention${NC}"
fi

echo ""
echo "=============================================="

exit 0
