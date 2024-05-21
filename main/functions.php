<?php
    session_start();
    if (!isset($_SESSION['LOGGED_USER'])) { header('Location:index.php'); }
    function P($var) {
        echo "<pre>";
        print_r($var);
        echo "</pre>";
    }

    function UserIsBlocked(int $attempts): bool { return $userIsBlocked = ($attempts >= 3); }

    function PasswordIsValid(string $username, string $password): ?string
    {
        $username = Sanitize($username);
        $password = Sanitize($password);
        
        // look for accents
        if (!preg_match('/[^\x20-\x7E]/', $password)) {
            // look for username
            if (!str_contains(strtolower($password), strtolower($username))) {
                $parameters = PasswordSelect();
                // look for password configuration parameters
                $countDigits = preg_match_all('/[0-9]/', $password);
                $countLowercase = preg_match_all('/[a-z]/', $password);
                $countUppercase = preg_match_all('/[A-Z]/', $password);
                $countSpecial = preg_match_all('/[!"#$%&\'()*+,\-./;<=>?@\\\^_`{|}~]/', $password);
                // compare the counts with the parameters
                if ($countDigits >= $parameters['n'] && $countLowercase >= $parameters['p'] && $countUppercase >= $parameters['q'] && $countSpecial >= $parameters['r']) {
                    return NULL; // valid password
                } else {
                    $errorMessage = 'Erreur : Le mot de passe ne respecte pas les paramètres de configuration.';
                }
            } else {
                $errorMessage = 'Erreur : Le mot de passe ne doit pas contenir le nom d\'utilisateur.';
            }
        } else {
            $errorMessage = 'Erreur : Le mot de passe ne doit pas contenir d\'accent.';
        }
        return $errorMessage;
    }

    function Sanitize($input) {
        return $sanitizedInput = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
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

    function DownloadChecklist(array $goodpractices)
    {
        $jsonVar = json_encode($var);

        if ($jsonVar === false) {
            exit(1);
        }

        $jsonVarEscaped = escapeshellarg($jsonVar);
        $command = "/usr/bin/python3 checklist_creator.py $jsonVarEscaped";
        $output = shell_exec($command);

        return $output;
    }

?>