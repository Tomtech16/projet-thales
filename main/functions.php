<?php
    session_start();
    if (!isset($_SESSION['LOGGED_USER'])) { header('Location:index.php'); }
    function P($var) {
        echo "<pre>";
        print_r($var);
        echo "</pre>";
    }

    function UserIsBlocked(int $attempts): bool { return $userIsBlocked = ($attempts >= 3); }

    function PasswordIsValid(string $username, string $password, array $parameters = NULL): bool
    {
        // look for accents
        if (!preg_match('/[^\x20-\x7E]/', $password)) {
            // look for username
            if (!str_contains($password, $username)) {
                if ($parameters === NULL) {
                    $parameters = PasswordSelect();
                }
                $countDigits = preg_match_all('/[0-9]/', $password);
                $countLowercase = preg_match_all('/[a-b]/', $password);
                $countUppercase = preg_match_all('/[A-B]/', $password);
                $countSpecial = preg_match_all('/[!"#$%&\'()*+,\-./;<=>?@\\\^_`{|}~]/', $password);
                // compare the counts with the parameters
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

    function Desanitize($input) {
        return htmlspecialchars_decode($input, ENT_QUOTES);
    }

    function StrContainsAnySubstring(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return TRUE;
            }
        }
        return FALSE;
    }

    function replaceLastOccurrence($string, $patternToSearch, $replacement) {
        $position = strrpos($string, $patternToSearch);
        if ($position !== false) {
            $string = substr_replace($string, $replacement, $position, strlen($patternToSearch));
        }
        return $string;
    }

?>