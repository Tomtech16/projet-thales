<?php
    function P($var) {
        echo "<pre>";
        print_r($var);
        echo "</pre>";
    }

    function UserIsBlocked(int $attempts): bool { return $userIsBlocked = ($attempts >= 3); }

    function PasswordIsValid(string $username, string $password, string $password2): ?string
    {
        $username = Sanitize($username);
        $password = Sanitize($password);
        $password2 = Sanitize($password2);

        if ($password === $password2) {
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
                        $errorMessage = 'Erreur !\n\nLe mot de passe ne respecte pas les paramètres de configuration.';
                    }
                } else {
                    $errorMessage = 'Erreur !\n\nLe mot de passe ne doit pas contenir le nom d\'utilisateur.';
                }
            } else {
                $errorMessage = 'Erreur !\n\nLe mot de passe ne doit pas contenir d\'accent.';
            }
        } else {
            $errorMessage = 'Erreur !\n\nLes deux mots de passe sont différents.';
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
        if ($erasedPrograms !== NULL) {
            foreach ($goodPractices as $key => &$goodPractice) {
                unset($goodPractice['goodpractice_id']);
            }
            unset($goodPractice);
        }
        
        $jsonGoodpractices = json_encode($goodpractices);

        if ($jsonGoodpractices === false) {
            return 'Erreur lors de l\'encodage JSON des bonnes pratiques.';
        }

        $jsonVarEscaped = escapeshellarg($jsonVar);
        $command = "/usr/bin/python3 checklist_creator.py $jsonVarEscaped";
        $output = shell_exec($command);

        return $output;
    }

    function getUserIP() {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipArray = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($ipArray as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return 'UNKNOWN';
    }

    function Logger(string $username, string $profile, int $evenementType, string $description): void
    {
        $ip = Sanitize(getUserIP());
        $log = 'Client IP Address ['.$ip.'] ';
        $log .= 'Username ['.Sanitize($username).'] ';
        $log .= 'Profile ['.Sanitize($profile).'] ';
        switch ($evenementType) {
            case 0 :
                $log .= 'Information [';
                break;
            case 1 :
                $log .= 'Warning [';
                break;
            case 2 :
                $log .= 'Alarm [';
                break;
        }
        $log .= Sanitize($description);
        $log .= ']';
        ini_set("error_log", "./log/log.txt");
        error_log($log);
    }

    function LogFilter(array $log, string $ip = NULL, string $username = NULL, array $profile = NULL, int $evenementType = NULL, string $wordsInDescription = NULL): array
    {
        return $log;
    }
?>