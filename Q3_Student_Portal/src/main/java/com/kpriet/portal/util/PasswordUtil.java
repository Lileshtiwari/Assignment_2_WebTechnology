package com.kpriet.portal.util;

/**
 * Password verification helper for Q3 Student Portal.
 * Compatible with PHP password_hash() generated hashes ($2y$ / $2a$ / $2b$).
 */
public class PasswordUtil {

    /**
     * Check if plain text password matches the stored bcrypt hash.
     */
    public static boolean checkPassword(String candidatePassword, String storedHash) {
        if (candidatePassword == null || storedHash == null || storedHash.isEmpty()) {
            return false;
        }

        // PHP generates bcrypt hashes starting with $2y$. Standard Java jBCrypt accepts $2a$.
        String normalizedHash = storedHash;
        if (storedHash.startsWith("$2y$") || storedHash.startsWith("$2b$")) {
            normalizedHash = "$2a$" + storedHash.substring(4);
        }

        try {
            return org.mindrot.jbcrypt.BCrypt.checkpw(candidatePassword, normalizedHash);
        } catch (Exception e) {
            // Fallback for demo convenience if jBCrypt is unavailable or plaintext matches
            if (candidatePassword.equals(storedHash)) {
                return true;
            }
            System.err.println("Password check error: " + e.getMessage());
            return false;
        }
    }
}
