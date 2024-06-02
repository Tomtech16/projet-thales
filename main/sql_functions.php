<?php    
    session_start();
    $path = $_SERVER['PHP_SELF'];
    $file = basename($path);
    require_once(__DIR__ . '/functions.php');
    if (!isset($_SESSION['LOGGED_USER']) && !isset($_SESSION['LOGIN_TENTATIVE'])) { Logger(NULL, NULL, 2, 'Unauthorized access attempt to '.$file); header('Location:logout.php'); exit(); }
    
    function PasswordSelect(): array
    {
        // Return data of PASSWORD table
        global $bd;

        $sql = "select * from PASSWORD";
        $stmt = $bd->prepare($sql);
        $stmt->execute() or die(print_r($stmt->errorInfo()));
        $passwordParameters = $stmt->fetchall();
        $stmt->closeCursor();
        foreach ($passwordParameters as $parameters) {
            return $parameters;
        }
    }

    function PasswordUpdate(int $n, int $p, int $q, int $r): void
    {
        // Update PASSWORD table
        global $bd;

        $sql = "UPDATE PASSWORD SET n = :n, p = :p, q = :q, r = :r";
        $stmt = $bd->prepare($sql);

        $stmt->bindParam(':n', $n, PDO::PARAM_INT);
        $stmt->bindParam(':p', $p, PDO::PARAM_INT);
        $stmt->bindParam(':q', $q, PDO::PARAM_INT);
        $stmt->bindParam(':r', $r, PDO::PARAM_INT);

        $stmt->execute();
    }

    function UsersSelect(array $orderBy = NULL, string $profile = NULL): array
    {
        // Return data of USER table
        global $bd;

        if ($profile === 'admin') {
            $sql = "SELECT * FROM USERS WHERE profile != 'superadmin' AND profile != 'admin'";
        } elseif ($profile === 'superadmin') {
            $sql = "SELECT * FROM USERS WHERE profile != 'superadmin'";
        } else {
            $sql = "SELECT * FROM USERS";
        }

        if ($orderBy !== NULL) {
            $order = $orderBy[0];
            $direction = $orderBy[1];
            if (($order === 'username' || $order === 'firstname' || $order === 'lastname' || $order === 'profile' || $order === 'attempts') && ($direction === 'asc' || $direction === 'desc'))
            $sql .= " ORDER BY {$order} {$direction}";
        }
    
        $stmt = $bd->prepare($sql);
        $stmt->execute() or die(print_r($stmt->errorInfo()));
    
        $users = $stmt->fetchAll();
        $stmt->closeCursor();
    
        return $users;
    }    

    function UserAttempts(int $userId, string $option): void 
    {
        /*  Option -->
                If $option === 'reset' : reset login attempts.
                If $option === 'increment' : increment login attempts.
        */

        global $bd;

        $sql = "update USERS ";
        if ($option === 'reset') {
            $sql .= "set attempts = 0 ";
        } elseif ($option === 'increment') {
            $sql .= "set attempts = attempts + 1 ";
        }   
        $sql .= "where user_id = :userId";
        $stmt = $bd->prepare($sql);
        $marqueurs = array('userId' => $userId);
        $stmt->execute($marqueurs) or die(print_r($stmt->errorInfo()));
        $stmt->closeCursor();
    }

    function UserIsInBDD(string $username): bool
    {
        // check if user is in USERS table
        $users = UsersSelect();
        foreach ($users as $user) {
            if ($username === $user['username']) {
                return TRUE;
            } 
        }
        return FALSE;
    }

    function UserAppend(string $username, string $firstname, string $lastname, string $password, string $profile = NULL): bool
    {
        // append user to USERS table

        global $bd;
        
        if (!UserIsInBDD($username)) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $sql = "insert into USERS (username, firstname, lastname, profile, password, attempts) values (:username, :firstname, :lastname, :profile, :password, 0)";
            $stmt = $bd->prepare($sql);
            $marqueurs = array('username' => Sanitize($username), 'firstname' => Sanitize($firstname), 'lastname' => Sanitize($lastname), 'password' => Sanitize($hash));
            if ($profile === NULL || $profile === 'operator') {
                $marqueurs['profile'] = 'operator';
            } elseif ($profile === 'admin') {
                $marqueurs['profile'] = 'admin';
            }
            $stmt->execute($marqueurs) or die(print_r($stmt->errorInfo()));
            $stmt->closeCursor();
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function UserWhatIsName(int $userId): string
    {
        global $bd;

        $sql = "SELECT username FROM USERS WHERE user_id = :userId;";
        $stmt = $bd->prepare($sql);
        $marqueurs['userId'] = Sanitize($userId);
        $stmt->execute($marqueurs) or die(print_r($stmt->errorInfo()));
        $username = $stmt->fetch();
        $stmt->closeCursor();
        return Sanitize($username[0]);
    }

    function UserDelete(int $userId, string $profile): void
    {   
        global $bd;

        if ($profile === 'superadmin') {
            $sql = "DELETE FROM USERS WHERE user_id = :userId AND profile != 'superadmin'";
        } elseif ($profile === 'admin') {
            $sql = "DELETE FROM USERS WHERE user_id = :userId AND profile = 'operator'";
        }
        $stmt = $bd->prepare($sql);
        $marqueurs['userId'] = Sanitize($userId);
        $stmt->execute($marqueurs) or die(print_r($stmt->errorInfo()));
        $stmt->closeCursor();
    }

    function UserResetPassword(int $userId, string $newPassword, string $profile): void
    {
        global $bd;
        
        $newHash = password_hash(Sanitize($newPassword), PASSWORD_BCRYPT);
        if ($profile === 'superadmin') {
            $sql = "UPDATE USERS SET password = :newHash, attempts = 0 WHERE user_id = :userId AND profile != 'superadmin'";
        } elseif ($profile === 'admin') {
            $sql = "UPDATE USERS SET password = :newHash, attempts = 0 WHERE user_id = :userId AND profile = 'operator'";
        }
        $stmt = $bd->prepare($sql);
        $marqueurs = array('newHash' => $newHash, 'userId' => Sanitize($userId));
        $stmt->execute($marqueurs) or die(print_r($stmt->errorInfo()));
        $stmt->closeCursor();
    }

    function GoodPracticesSelect(array $whereIs = NULL, array $orderBy = NULL, array $erasedGoodpractices = NULL, array $erasedPrograms = NULL, string $profile): array
    {
        // Return data of tables :
        //     - GOODPRACTICE(goodpratice_id)
        //     - PROGRAM(program_names)
        //     - PHASE(phase_name)
        //     - GOODPRACTICE(item)
        //     - KEYWORD(keywords)
            
        // Parameters :
        
        //     - $whereIs = array($column = array($filter1, $filter2...))

        //         -- $column :
        //             - 'goodpractice_id'
        //             - 'program_name'
        //             - 'phase_name'
        //             - 'item'
        //             - 'onekeyword'

        //         -- $value :
        //             - The filters to apply.
                    
        //         ==> WHERE $column = $value
            
        //     - $orderBy = array($column => $ascending)

        //         -- $ascending :
        //             - TRUE
        //             - FALSE

        //         ==> ORDER BY $column [ DESC if ($ascending === FALSE) ]

        //     - $erasedGoodpractices = array of goodpractice_ids to exclude

        global $bd;

        if ($profile !== 'admin' && $profile !== 'superadmin') {
            $sql .= " 
                SELECT 
                    GOODPRACTICE.goodpractice_id,
                    GROUP_CONCAT(DISTINCT PROGRAM.program_name ORDER BY PROGRAM.program_name SEPARATOR ', ') AS program_names,
                    PHASE.phase_name,
                    GOODPRACTICE.item,
                    GROUP_CONCAT(DISTINCT KEYWORD.onekeyword ORDER BY KEYWORD.onekeyword SEPARATOR ', ') AS keywords
                FROM GOODPRACTICE
                INNER JOIN PHASE ON GOODPRACTICE.phase_id = PHASE.phase_id
                INNER JOIN GOODPRACTICE_PROGRAM ON GOODPRACTICE.goodpractice_id = GOODPRACTICE_PROGRAM.goodpractice_id
                INNER JOIN PROGRAM ON GOODPRACTICE_PROGRAM.program_id = PROGRAM.program_id
                INNER JOIN GOODPRACTICE_KEYWORD ON GOODPRACTICE.goodpractice_id = GOODPRACTICE_KEYWORD.goodpractice_id
                INNER JOIN KEYWORD ON GOODPRACTICE_KEYWORD.keyword_id = KEYWORD.keyword_id
                WHERE GOODPRACTICE.is_hidden = FALSE AND GOODPRACTICE_PROGRAM.is_hidden = FALSE
            ";
        } else {
            $sql = "
                SELECT 
                    GOODPRACTICE.goodpractice_id,
                    GOODPRACTICE.is_hidden AS goodpractice_is_hidden,
                    GROUP_CONCAT(DISTINCT CONCAT(PROGRAM.program_name, ':', GOODPRACTICE_PROGRAM.is_hidden) ORDER BY PROGRAM.program_name SEPARATOR ', ') AS program_names,
                    PHASE.phase_name,
                    GOODPRACTICE.item,
                    GROUP_CONCAT(DISTINCT KEYWORD.onekeyword ORDER BY KEYWORD.onekeyword SEPARATOR ', ') AS keywords
                FROM GOODPRACTICE
                INNER JOIN PHASE ON GOODPRACTICE.phase_id = PHASE.phase_id
                INNER JOIN GOODPRACTICE_PROGRAM ON GOODPRACTICE.goodpractice_id = GOODPRACTICE_PROGRAM.goodpractice_id
                INNER JOIN PROGRAM ON GOODPRACTICE_PROGRAM.program_id = PROGRAM.program_id
                INNER JOIN GOODPRACTICE_KEYWORD ON GOODPRACTICE.goodpractice_id = GOODPRACTICE_KEYWORD.goodpractice_id
                INNER JOIN KEYWORD ON GOODPRACTICE_KEYWORD.keyword_id = KEYWORD.keyword_id
            ";
        }

        $params = array(); // Array to store parameter values

        // Check for WHERE clause
        if ($whereIs !== NULL) {
            if ($profile !== 'admin' && $profile !== 'superadmin') {
                $whereClauseStart .= " AND ( ";
            } else {
                $whereClauseStart .= " WHERE ( ";
            }
            $whereClause = '';
            foreach ($whereIs as $column => $filters) {
                if (!empty($column[0]) && !empty($filters[0])) {
                    foreach ($filters as $index => $value) {
                        if (!empty($value)) {
                            $paramName = ":$column$index"; // Unique parameter name
                            $whereClause .= "$column = $paramName OR ";
                            $params[$paramName] = $value; // Store parameter value
                        }
                    }
                    $whereClause = ReplaceLastOccurrence('OR ', '', $whereClause);
                    $whereClause .= (') AND ( ');
                } 
            }
            $whereClause = ReplaceLastOccurrence(' AND ( ', '', $whereClause);
            $whereClause = $whereClauseStart.$whereClause;
            if ($whereClause !== ' AND ( ' && $whereClause !== ' WHERE ( ') {
                $sql .= $whereClause;
            }
        }

        // Check for excluded good practices
        if ($erasedGoodpractices !== NULL) {
            // Construct the IN clause for excluded goodpractice_ids
            $excludedIds = implode(", ", $erasedGoodpractices);
            $sql .= " AND GOODPRACTICE.goodpractice_id NOT IN ($excludedIds)";
        }
        
        $sql .= ' GROUP BY GOODPRACTICE.item';

        // Check for ORDER BY clause
        if ($orderBy !== NULL) {
            $order = $orderBy[0];
            $direction = $orderBy[1];
            if (($order === 'program_names' || $order === 'phase_name' || $order === 'item' || $order === 'keywords') && ($direction === 'asc' || $direction === 'desc')) {
                $sql .= " ORDER BY {$order} {$direction}";
            }
        }
        $sql .= ";";
        // Prepare and execute the SQL query
        $stmt = $bd->prepare($sql);
        // Bind values to parameters
        foreach ($params as $paramName => $value) {
            $stmt->bindValue($paramName, $value);
        }
        $stmt->execute() or die(print_r($stmt->errorInfo()));
        $goodPractices = $stmt->fetchAll();
        $stmt->closeCursor();

        if ($erasedPrograms !== NULL) {
            foreach ($goodPractices as $key => &$goodPractice) {
                $goodPractice['program_names'] = EraseProgramNames($goodPractice['program_names'], $erasedPrograms['id'.$goodPractice['goodpractice_id']], $profile);
                if (empty($goodPractice['program_names'])) {
                    unset($goodPractices[$key]);
                }
            }
            unset($goodPractice);
        }

        return $goodPractices;
    }

    function ProgramSelect(string $all = NULL): array
    {
        global $bd;
        if ($all === 'all') {
            $sql = "SELECT * FROM PROGRAM";
        } else {
            $sql = "SELECT program_name FROM PROGRAM";
        }
        $stmt = $bd->prepare($sql);
        $stmt->execute();
        $programs = $stmt->fetchAll();
        $stmt->closeCursor();
        return $programs;
    }

    function PhaseSelect(string $all = NULL): array
    {
        global $bd;
        if ($all === 'all') {
            $sql = "SELECT * FROM PHASE";
        } else {
            $sql = "SELECT phase_name FROM PHASE";
        }
        $stmt = $bd->prepare($sql);
        $stmt->execute();
        $phases = $stmt->fetchAll();
        $stmt->closeCursor();
        return $phases;
    }

    function KeywordSelect(string $all = NULL): array
    {
        global $bd;
        if ($all === 'all') {
            $sql = "SELECT * FROM KEYWORD";
        } else {
            $sql = "SELECT onekeyword FROM KEYWORD";
        }
        $stmt = $bd->prepare($sql);
        $stmt->execute();
        $keywords = $stmt->fetchAll();
        $stmt->closeCursor();
        return $keywords;
    }

    function ValidateKeywordsSelection(string $keywordsSelection = NULL): array
    {   
        if ($keywordsSelection === NULL || empty($keywordsSelection)) {
            return array(array(''),array(''));
        }
        $keywordSelect = KeywordSelect();
        foreach ($keywordSelect as $keyword) {
            $keywords[] = $keyword[0];
        }
        $keywordsSelection = explode(', ', $keywordsSelection);
        $wrongKeywords = array_diff($keywordsSelection, $keywords);
        $keywordsSelection = array_diff($keywordsSelection, $wrongKeywords);
        return array($keywordsSelection, $wrongKeywords);
    }

    // Function to insert a new good practice item and return its ID
    function InsertGoodpracticeItem(string $item, int $phaseId): int
    {
        global $bd;
        $stmt = $bd->prepare("INSERT INTO GOODPRACTICE (item, phase_id) VALUES (:item, :phaseId)");
        $stmt->bindParam(':item', Desanitize($item), PDO::PARAM_STR);
        $stmt->bindParam(':phaseId', Desanitize($phaseId), PDO::PARAM_INT);
        $stmt->execute();
        return $bd->lastInsertId();
    }

    // Function to insert an entry into the junction table GOODPRACTICE_PROGRAM
    function InsertGoodpracticeProgram(int $goodpracticeId, int $programId): void
    {
        global $bd;
        $stmt = $bd->prepare("INSERT INTO GOODPRACTICE_PROGRAM (goodpractice_id, program_id) VALUES (:goodpracticeId, :programId)");
        $stmt->bindParam(':goodpracticeId', Desanitize($goodpracticeId), PDO::PARAM_INT);
        $stmt->bindParam(':programId', Desanitize($programId), PDO::PARAM_INT);
        $stmt->execute();
    }

    // Function to insert an entry into the junction table GOODPRACTICE_KEYWORD
    function InsertGoodpracticeKeyword(int $goodpracticeId, int $keywordId): void
    {
        global $bd;
        $stmt = $bd->prepare("INSERT INTO GOODPRACTICE_KEYWORD (goodpractice_id, keyword_id) VALUES (:goodpracticeId, :keywordId)");
        $stmt->bindParam(':goodpracticeId', Desanitize($goodpracticeId), PDO::PARAM_INT);
        $stmt->bindParam(':keywordId', Desanitize($keywordId), PDO::PARAM_INT);
        $stmt->execute();
    }

    // Function to get the ID of the phase or insert a new phase if it does not exist
    function GetPhaseId(string $phaseName): int
    {
        global $bd;
        $stmt = $bd->prepare("SELECT phase_id FROM PHASE WHERE phase_name = :phaseName");
        $stmt->bindParam(':phaseName', Desanitize($phaseName), PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result !== false) {
            return $result['phase_id'];
        } else {
            // Phase does not exist, insert it
            return InsertPhase($phaseName);
        }
    }

    // Function to insert a new phase and return its ID
    function InsertPhase(string $phaseName): int
    {
        global $bd;
        $stmt = $bd->prepare("INSERT INTO PHASE (phase_name) VALUES (:phaseName)");
        $stmt->bindParam(':phaseName', Desanitize($phaseName), PDO::PARAM_STR);
        $stmt->execute();
        return $bd->lastInsertId();
    }

    // Function to get the ID of the program or insert a new program if it does not exist
    function GetProgramId(string $programName): int
    {
        global $bd;
        $stmt = $bd->prepare("SELECT program_id FROM PROGRAM WHERE program_name = :programName");
        $stmt->bindParam(':programName', Desanitize($programName), PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result !== false) {
            return $result['program_id'];
        } else {
            // Program does not exist, insert it
            return InsertProgram($programName);
        }
    }

    // Function to insert a new program and return its ID
    function InsertProgram(string $programName): int
    {
        global $bd;
        $stmt = $bd->prepare("INSERT INTO PROGRAM (program_name) VALUES (:programName)");
        $stmt->bindParam(':programName', Desanitize($programName), PDO::PARAM_STR);
        $stmt->execute();
        return $bd->lastInsertId();
    }

    // Function to get the ID of the keyword or insert a new keyword if it does not exist
    function GetKeywordId(string $keyword): int
    {
        global $bd;
        $stmt = $bd->prepare("SELECT keyword_id FROM KEYWORD WHERE onekeyword = :keyword");
        $stmt->bindParam(':keyword', Desanitize($keyword), PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result !== false) {
            return $result['keyword_id'];
        } else {
            // Keyword does not exist, insert it
            return InsertKeyword($keyword);
        }
    }

    // Function to insert a new keyword and return its ID
    function InsertKeyword(string $keyword): int
    {
        global $bd;
        $stmt = $bd->prepare("INSERT INTO KEYWORD (onekeyword) VALUES (:keyword)");
        $stmt->bindParam(':keyword', Desanitize($keyword), PDO::PARAM_STR);
        $stmt->execute();
        return $bd->lastInsertId();
    }

    // Main function to insert a good practice with associated programs and keywords
    function InsertGoodpractice(array $programNames, string $phaseName, string $item, array $keywords): void
    {
        global $bd;
        // Check if the phase exists, otherwise insert it
        $phaseId = GetPhaseId($phaseName);
        // Insert the good practice
        $goodpracticeId = InsertGoodpracticeItem($item, $phaseId);
        // Associate the good practice with programs
        foreach ($programNames as $programName) {
            $programId = GetProgramId($programName);
            InsertGoodpracticeProgram($goodpracticeId, $programId);
        }
        // Associate the good practice with keywords
        foreach ($keywords as $keyword) {
            $keywordId = GetKeywordId($keyword);
            InsertGoodpracticeKeyword($goodpracticeId, $keywordId);
        }
    }

    function DuplicateGoodpractice(array $programNames, int $goodpracticeId): void
    {
        foreach ($programNames as $programName) {
            $programId = GetProgramId(Sanitize($programName));
            InsertGoodpracticeProgram($goodpracticeId, $programId);
        }
    }

    function EraseProgramNames(string $programNames, array $erasedProgramNames = NULL, string $profile): string
    {
        if ($erasedProgramNames !== NULL) {
            if ($profile !== 'admin' && $profile !== 'superadmin') {
                return Sanitize(implode(', ', array_diff(explode(', ', $programNames), $erasedProgramNames)));
            } else {
                $programArray = [];
                foreach (explode(', ', $programNames) as $programName) {
                    $programArray[$programName] = substr($programName, 0, -2);
                }
                return Sanitize(implode(', ',array_keys(array_diff($programArray, $erasedProgramNames))));
            }
        } else {
            return Sanitize($programNames);
        }
    }

    function DeleteGoodpractice(int $goodpracticeId, array $programNames = NULL): void
    {
        global $bd; // Assuming $bd is your PDO connection
    
        try {
            $bd->beginTransaction();
    
            if (empty($programNames)) {
                // If $programNames is empty, delete all related rows
                $sql1 = "DELETE FROM GOODPRACTICE_PROGRAM WHERE goodpractice_id = :goodpractice_id;";
                $sql2 = "DELETE FROM GOODPRACTICE_KEYWORD WHERE goodpractice_id = :goodpractice_id;";
                $sql3 = "DELETE FROM GOODPRACTICE WHERE goodpractice_id = :goodpractice_id;";
                
                $stmt1 = $bd->prepare($sql1);
                $stmt1->bindValue(':goodpractice_id', $goodpracticeId, PDO::PARAM_INT);
                $stmt1->execute();
                $stmt1->closeCursor();
    
                $stmt2 = $bd->prepare($sql2);
                $stmt2->bindValue(':goodpractice_id', $goodpracticeId, PDO::PARAM_INT);
                $stmt2->execute();
                $stmt2->closeCursor();
    
                $stmt3 = $bd->prepare($sql3);
                $stmt3->bindValue(':goodpractice_id', $goodpracticeId, PDO::PARAM_INT);
                $stmt3->execute();
                $stmt3->closeCursor();
            } else {
                // If $programNames is not empty, delete rows based on goodpractice_id and program_id
                $sql = "DELETE FROM GOODPRACTICE_PROGRAM WHERE goodpractice_id = :goodpractice_id AND program_id = :program_id";
                foreach ($programNames as $programName) {
                    $programId = GetProgramId($programName);
                    $stmt = $bd->prepare($sql);
                    $stmt->bindValue(':goodpractice_id', $goodpracticeId, PDO::PARAM_INT);
                    $stmt->bindValue(':program_id', $programId, PDO::PARAM_INT);
                    $stmt->execute();
                    $stmt->closeCursor();
                }
            }
    
            $bd->commit();
        } catch (PDOException $e) {
            $bd->rollBack();
            // Log or handle the exception as needed
            throw new Exception("Error deleting good practice: " . $e->getMessage());
        }
    }

    function DeleteOperatorGoodpractice(int $goodpracticeId, array $programNames = NULL): void
    {
        global $bd; // Assuming $bd is your PDO connection
    
        try {
            $bd->beginTransaction();
    
            if (empty($programNames)) {
                // If $programNames is empty, hide the good practice
                $sql = "UPDATE GOODPRACTICE SET is_hidden = TRUE WHERE goodpractice_id = :goodpractice_id;";
                $stmt = $bd->prepare($sql);
                $stmt->bindValue(':goodpractice_id', $goodpracticeId, PDO::PARAM_INT);
                $stmt->execute();
                $stmt->closeCursor();
            } else {
                // If $programNames is not empty, delete rows based on goodpractice_id and program_id
                $sql = "UPDATE GOODPRACTICE_PROGRAM SET is_hidden = TRUE WHERE goodpractice_id = :goodpractice_id AND program_id = :program_id";
                foreach ($programNames as $programName) {
                    $programId = GetProgramId($programName);
                    $stmt = $bd->prepare($sql);
                    $stmt->bindValue(':goodpractice_id', $goodpracticeId, PDO::PARAM_INT);
                    $stmt->bindValue(':program_id', $programId, PDO::PARAM_INT);
                    $stmt->execute();
                    $stmt->closeCursor();
                }
            }
    
            $bd->commit();
        } catch (PDOException $e) {
            $bd->rollBack();
            // Log or handle the exception as needed
            throw new Exception("Error deleting good practice: " . $e->getMessage());
        }
    }

    function RestoreGoodpractice(int $goodpracticeId): void
    {
        global $bd;
        $sql = "
            UPDATE GOODPRACTICE SET is_hidden = FALSE WHERE goodpractice_id = :goodpractice_id;
            UPDATE GOODPRACTICE_PROGRAM SET is_hidden = FALSE WHERE goodpractice_id = :goodpractice_id;
        ";
        $stmt = $bd->prepare($sql);
        $stmt->bindValue(':goodpractice_id', $goodpracticeId, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
?>