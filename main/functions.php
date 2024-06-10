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
                $accents = array(
                    'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð',
                    'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã',
                    'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ',
                    'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ',
                    'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę',
                    'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī',
                    'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ',
                    'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ',
                    'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 
                    'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 
                    'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ',
                    'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ'
                );
                if (!StrContainsAnySubstring($password, $accents)) {
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


    function DownloadChecklist(array $whereIs = NULL, array $orderBy = NULL, array $erasedGoodpractices = NULL, array $erasedPrograms = NULL, string $username = NULL, string $profile, string $mode): array
    {
        // Convert PHP arrays to JSON strings
        $whereIs = $whereIs ? json_encode($whereIs) : '';
        $whereIs = str_replace('"program_name":null,', '', $whereIs);
        $whereIs = str_replace('"phase_name":null,', '', $whereIs);
        $whereIs = str_replace('{"keywords":[""],', '', $whereIs);
        $orderBy = $orderBy ? json_encode($orderBy) : '';
        $erasedGoodpractices = $erasedGoodpractices ? implode(',', $erasedGoodpractices) : '';
        $erasedPrograms = $erasedPrograms ? json_encode($erasedPrograms) : '';
        $profile = Sanitize($profile);
        $username = $username ? Sanitize($username) : '';
    
        // Construct command to execute Python script
        $currentDirectoryPath = getcwd();
        $checklistFilesDirectory = $currentDirectoryPath . "/checklist";
        require_once(__DIR__ . '/config/paths.php');
        $pythonChecklistGeneratorProgramPath = $currentDirectoryPath . "/python/checklist_generator.py";

        $command = "cd " . $checklistFilesDirectory . " && " . $python3BinaryPath . " " . $pythonChecklistGeneratorProgramPath . " ";
        $command .= "--where " . escapeshellarg($whereIs) . " ";
        $command .= "--order " . escapeshellarg($orderBy) . " ";
        $command .= "--erased_goodpractices " . escapeshellarg($erasedGoodpractices) . " ";
        $command .= "--erased_programs " . escapeshellarg($erasedPrograms) . " ";        
        if ($username) {
            $command .= " --username " . escapeshellarg($username) . " ";
        }
        $command .= "--profile " . escapeshellarg($profile) . " ";
        $outputFile = "checklist_" . ($username ? $username . '_' : '') . date('d-m-Y') . ($mode === 'pdf' ? '.pdf' : '.csv');
        $command .= "--output_format " . escapeshellarg($mode) . " ";
        $command .= "--output_file " . escapeshellarg($outputFile);

        // Execute the command
        exec($command, $output, $exit_code);
        $filename = $output[0];

        if (intval($exit_code) === 0) {                
            Logger($username, $profile, 0, "Successfully generated {$mode} checklist with filename : $filename");
            return array('Succès !\n\nLa checklist : ' . $filename . ', au format ' . strtoupper($mode) . ' a bien été générée.', $filename);
        } else {
            Logger($username, $profile, 2, "Failed to generate {$mode} checklist with filename : $filename");
            return array('Erreur !\n\nLe programme python n\'a pas réussi à générer la checklist : ' . $filename . ', au format ' . strtoupper($mode) . '.', $filename);
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