<?php
    session_start();
    $path = $_SERVER['PHP_SELF'];
    $file = basename($path);
    if (!isset($_SESSION['LOGGED_USER']) && !isset($_SESSION['LOGIN_TENTATIVE']) && !isset($_SESSION['LOGOUT_TENTATIVE'])) { Logger(NULL, NULL, 2, 'Unauthorized access attempt to '.$file); header('Location:logout.php'); exit(); }
    
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
            // look for spaces
            if (!str_contains($password, ' ')) {
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
                        $errorMessage = 'Erreur !\n\nLe mot de passe ne doit pas contenir le nom d utilisateur.';
                    }
                } else {
                    $errorMessage = 'Erreur !\n\nLe mot de passe ne doit pas contenir d accent.';
                }
            } else {
                $errorMessage = 'Erreur !\n\nLe mot de passe ne doit pas contenir d espace.';
            }
        } else {
            $errorMessage = 'Erreur !\n\nLes deux mots de passe sont différents.';
        }   
        return $errorMessage;
        
    }

    function Sanitize($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
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

    function StrContainsAnySubstringApprox(string $haystack, array $needles): bool
    {
        $haystack = str_replace('[', '', $haystack);
        $haystack = str_replace(']', '', $haystack);
        $haystack = strtolower($haystack);
        $haystackExploded = explode(' ', Sanitize($haystack));
        foreach ($haystackExploded as $oneHaystack) {
            foreach ($needles as $needle) {
                similar_text($oneHaystack, strtolower($needle), $percentage);
                if ($percentage >= 80) {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }

    function ReplaceLastOccurrence($patternToSearch, $replacement, $string): string
    {   
        $position = strrpos($string, $patternToSearch);
        if ($position !== false) {
            $string = substr_replace($string, $replacement, $position, strlen($patternToSearch));
        }
        return $string;
    }

    function DownloadChecklist(array $goodpracticesParameters, string $username, string $profile): string
    {
        $jsonData = json_encode($goodpracticesParameters);

        if ($jsonGoodpractices === false) {
            Logger(Sanitize($username), Sanitize($profile), 2, 'Failed to generate checklist, failed to json encode python parameters');
            return "Erreur !\n\nL'encodage JSON des paramètres du programme python a échouée.";
        } else {
            $file = './json/python_parameters.json';
            if (file_put_contents($file, $jsonData)) {
                $output =  shell_exec('/usr/bin/python3 checklist_generator.py');
                if (!$output) {
                    Logger(Sanitize($username), Sanitize($profile), 0, 'Successfuly generate checklist');
                    return "Succès !\n\nLa checklist a bien été générée.";
                } else {
                    Logger(Sanitize($username), Sanitize($profile), 2, $output);
                    return "Erreur !\n\nLe programme python n'a pas réussi à générer la checklist.";
                }
            } else {
                Logger(Sanitize($username), Sanitize($profile), 2, 'Failed to generate checklist, failed to write python parameters in json file');
                return "Erreur !\n\nL'écriture des paramètres python dans le fichier json a échouée.";
            }
        }
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

    function Logger(string $username = NULL, string $profile = NULL, int $evenementType, string $description): void
    {
        $u = 0;
        $ip = Sanitize(getUserIP());
        $log = '['.$ip.'] ';
        if ($username !== NULL && $profile !== NULL) {
            $log .= '['.Sanitize($username).'] ';
            switch (Sanitize($profile)) {
                case 'operator' :
                    $log .= '[Operator] ';
                    break;
                case 'admin' :
                    $log .= '[Admin] ';
                    break;
                case 'superadmin' :
                    $log .= '[Superadmin] ';
                    break;
            }
        } else {
            $log .= '[Unauthenticated] [Unauthenticated] ';
        }
        switch ($evenementType) {
            case 0 :
                $log .= '[Information] ';
                break;
            case 1 :
                $log .= '[Warning] ';
                break;
            case 2 :
                $log .= '[Alarm] ';
                break;
        }
        $log .= '['.Sanitize($description).']';
        ini_set("error_log", "./log/log.txt");
        error_log($log);
    }

    function LogFilter(string $userUsername, string $userProfile, array $log, int $day = NULL, string $month = NULL, int $year = NULL, array $evenementType = NULL, array $profiles = NULL, string $logSearch = NULL): ?array
    {
        $userProfile = Sanitize($userProfile);
        if ($userProfile === 'admin' || $userProfile === 'superadmin') {
            if ($evenementType !== NULL) {
                foreach ($evenementType as $evenement) {
                    if ($evenement === 'Information' || $evenement === 'Warning' || $evenement = 'Alarm') {
                        $logFilters['evenement'][] = '['.$evenement.']';
                    }
                }
            }
            if ($profiles !== NULL) {
                foreach ($profiles as $profile) {
                    if ($profile === 'Operator' || $profile === 'Admin' || $profile === 'Superadmin') {
                        $logFilters['profile'][] = '['.$profile.']';
                    }
                }
            }
            if ($logSearch !== NULL && !empty($logSearch)) {
                $logFilters['search'] = explode(', ', Sanitize($logSearch));
            }
            if (isset($logFilters) && !empty($logFilters)) {
                $userUsername = Sanitize($userUsername);
                $filterNumber = count($logFilters);
                foreach ($log as $logLine) {
                    if (LogIsInTime($logLine, $day, $month, $year)) {
                        $passedFilterNumber = 0;
                        $logLine = Sanitize($logLine);
                        if ($userProfile === 'admin' && ((!str_contains($logLine, '[Admin]') || str_contains($logLine, $userUsername)) && !str_contains($logLine, '[Superadmin]'))) {
                            foreach ($logFilters as $logFilter) {
                                if (StrContainsAnySubstringApprox($logLine, $logFilter)) { 
                                    $passedFilterNumber += 1;
                                }
                            }
                            if ($passedFilterNumber === $filterNumber) {
                                $logFiltered[] = $logLine;
                            }
                        } elseif ($userProfile === 'superadmin') {
                            foreach ($logFilters as $logFilter) {
                                if (StrContainsAnySubstringApprox($logLine, $logFilter)) { 
                                    $passedFilterNumber += 1;
                                }
                            }
                            if ($passedFilterNumber === $filterNumber) {
                                $logFiltered[] = $logLine;
                            }
                        }
                    }
                }
                return $logFiltered;
            } elseif ($day !== NULL || $month !== NULL || $year !== NULL) {
                foreach ($log as $logLine) {
                    $logLine = Sanitize($logLine);
                    if (LogIsInTime($logLine, $day, $month, $year)) {
                        if ($userProfile === 'admin' && ((!str_contains($logLine, '[Admin]') || str_contains($logLine, $userUsername)) && !str_contains($logLine, '[Superadmin]'))) {
                            $logFiltered[] = $logLine; 
                        } elseif ($userProfile === 'superadmin') {
                            $logFiltered[] = $logLine;
                        }
                    }
                }
                return $logFiltered;
            } else {
                return $log;
            }
        }
    }

    function LogIsInTime($logLine, int $day = NULL, string $month = NULL, int $year = NULL): bool
    {   
        $logLine = Sanitize($logLine);
        $day = Sanitize($day);
        $month = Sanitize($month);
        $year = Sanitize($year);
        $position = strpos($logLine, ' ');
        $logTimeLine = substr($logLine, 1, $position);
        if (!($day === NULL || $day < 1 || $day > 31)) {
            if ($day < 10) {
                $day = '0'.$day;
            }
            if (!(substr($logTimeLine, 0, 2) === strval($day))) {
                return FALSE;
            }
        }
        if (!($month === NULL || $month === '' || str_contains($logTimeLine, $month))) {
            return FALSE;
        }
        if (!($year === NULL || $year < 2024 || str_contains($logTimeLine, strval($year)))) {
            return FALSE;
        }
        return TRUE;
    }
?>