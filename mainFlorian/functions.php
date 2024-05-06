<?php

    function UserIsBlocked(int $attempts): bool { return $userIsBlocked = ($attempts >= 3); }

    function PasswordIsValid(string $username, string $password, array $parameters = NULL): bool
        {
        if (!preg_match('/[^\x20-\x7E]/', $password)) {
            if (!str_contains($password, $username)) {
                if ($parameters === NULL) {
                    $parameters = PasswordSelect();
                }
                $countDigits = preg_match_all('/[0-9]/', $password);
                $countLowercase = preg_match_all('/[a-b]/', $password);
                $countUppercase = preg_match_all('/[A-B]/', $password);
                $countSpecial = preg_match_all('/[!"#$%&\'()*+,\-./;<=>?@\\\^_`{|}~]/', $password);
                if ($countDigits >= $parameters['n'] || $countLowercase >= $parameters['p'] || $countUppercase >= $parameters['q'] || $countSpecial >= $parameters['r']) {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }

    function Sanitize($input) {
        return $sanitizedInput = htmlspecialchars($input, ENT_QUOTES);
    }

?>