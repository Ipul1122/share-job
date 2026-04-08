# Issue: Token Invalid Error on Password Reset

## Bug Description
When users attempt to reset their password using the reset link from email, they receive an error message "Token tidak valid atau sudah kedaluwarsa. Silakan minta link baru." even though the token should be valid.

## Error Message
```
Token tidak valid atau sudah kedaluwarsa. Silakan minta link baru.
```

## Affected Files
- `user/resetPassword.php` - Line 13 (token validation check)
- `user/lupaPassword.php` - Line 24 (reset link generation)

## Steps to Reproduce
1. User clicks "Lupa Password" on login page
2. User enters registered email address
3. User receives password reset email with link
4. User clicks the reset link
5. Page displays error: "Token tidak valid atau sudah kedaluwarsa..."

## Root Cause
The reset token validation query in `resetPassword.php` is not matching tokens correctly due to:
- Token not being properly escaped before database query
- Possible character encoding issues with token storage/retrieval
- Expired token check may have timezone or date format issues

## Expected Behavior
- Token should be validated successfully within the 1-hour expiry window
- User should be able to access the password reset form
- Password reset should complete successfully

## Current Behavior
- Token validation fails immediately
- User cannot proceed with password reset
- User must request a new reset link

## Suggested Fix
- Ensure token is properly escaped with `mysqli_real_escape_string()` before querying database
- Verify token expiry time is correctly set and retrieved from database
- Add debug logging to verify token storage and retrieval values match
- Consider implementing prepared statements for better security
