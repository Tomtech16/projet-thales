<?php    

    function PasswordSelect(): array
    {
        // return data of PASSWORD table

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

    function UsersSelect(): array
    {
        // return data of USERS table

        global $bd;

        $sql = "select * from USERS";
        $stmt = $bd->prepare($sql);
        $stmt->execute() or die(print_r($stmt->errorInfo()));
        $users = $stmt->fetchall();
        $stmt->closeCursor();
        return $users;

    }

    function UserAttempts(int $primaryKey, string $option): void 
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
        $sql .= "where userkey = :primarykey";
        $stmt = $bd->prepare($sql);
        $marqueurs = array('primarykey' => $primaryKey);
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

    function UserAppend(string $username, string $firstname, string $lastname, string $password): bool
    {
        // append user to USERS table

        global $bd;
        
        if (!UserIsInBDD($username)) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $sql = "insert into USERS (username, firstname, lastname, profile, password, attempts) values (:username, :firstname, :lastname, 'operator', :password, 0)";
            $stmt = $bd->prepare($sql);
            $marqueurs = array('username' => $username, 'firstname' => $firstname, 'lastname' => $lastname, 'password' => $hash);
            $stmt->execute($marqueurs) or die(print_r($stmt->errorInfo()));
            $stmt->closeCursor();
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function GoodPracticesSelect(array $whereIs = NULL, array $orderBy = NULL, array $deletedGoodpractices = NULL): array
    {
        // Return data of tables :
        //     - PROGRAM(program_names)
        //     - PHASE(phase_name)
        //     - GOODPRACTICE(item)
        //     - KEYWORD(keywords)
            
        // Parameters :
        
        //     - $whereIs = array($column => $value)

        //         -- $column :
        //             - 'program_names'
        //             - 'phase_name'
        //             - 'item'
        //             - 'keywords'

        //         -- $value :
        //             - The filter to apply.
                    
        //         ==> WHERE $column = $value
            
        //     - $orderBy = array($column => $ascending)

        //         -- $ascending :
        //             - TRUE
        //             - FALSE

        //         ==> ORDER BY $column [ DESC if ($ascending === FALSE) ]

        //     - $deletedGoodpractices = array of goodpractice_ids to exclude

        global $bd;
    
        $sql = "
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
        ";
    
        // Check for WHERE clause
        if ($whereIs != NULL) {
            $whereClause = " WHERE ";
        
            // Loop through key-value pairs in $whereIs array
            foreach ($whereIs as $where => $is) {
                // Add the condition to WHERE clause with parameterized values
                $whereClause .= "$where=:$where AND ";
            }
        
            // Remove the last 'AND' if it exists
            $whereClause = rtrim($whereClause, 'AND ');
            $sql .= $whereClause;
        }

        // Check for excluded good practices
        if ($deletedGoodpractices != NULL) {
            // Construct the IN clause for excluded goodpractice_ids
            $excludedIds = implode(",", $deletedGoodpractices);
            $sql .= " AND GOODPRACTICE.goodpractice_id NOT IN (".implode(",", array_fill(0, count($deletedGoodpractices), "?")).")";
        }
        
        $sql .= ' GROUP BY GOODPRACTICE.item';
    
        // Check for ORDER BY clause
        if ($orderBy != NULL) {
            $orderClause = " ORDER BY ";
            $oneBy = FALSE;

            foreach ($orderBy as $by => $dir) {
                if ($by === 'program_names' || $by === 'phase_name' || $by === 'item' || $by === 'keywords' || $by === 'GOODPRACTICE.goodpractice_id') {
                    $oneBy = TRUE;
                    if ($dir === TRUE) {
                        $orderClause .= "$by, ";
                    } else {
                        $orderClause .= "$by DESC, ";
                    }
                }
            }
            if ($oneBy) {
                // Remove the last comma if it exists
                $orderClause = rtrim($orderClause, ', ');
                $sql .= $orderClause;
            }
        }
    
        $sql .= ";";
        
        // Prepare and execute the SQL query
        $stmt = $bd->prepare($sql);
    
        // Bind values to parameters in WHERE clause
        if ($whereIs != NULL) {
            foreach ($whereIs as $where => $is) {
                $stmt->bindParam(":$where", $is);
            }
        }

        // Bind values to parameters in excluded good practices
        if ($deletedGoodpractices != NULL) {
            foreach ($deletedGoodpractices as $index => $value) {
                $stmt->bindValue(($index+1), $value);
            }
        }
    
        // For tests
        print_r($sql);
    
        $stmt->execute() or die(print_r($stmt->errorInfo()));
        $goodPractices = $stmt->fetchAll();
        $stmt->closeCursor();
        return $goodPractices;
    }

    // Fonction pour insérer une nouvelle bonne pratique et retourner son ID
    function InsertGoodpracticeItem(string $item, int $phaseId): int
    {
        global $bd;
        $stmt = $bd->prepare("INSERT INTO GOODPRACTICE (item, phase_id) VALUES (:item, :phaseId)");
        $stmt->bindParam(':item', $item, PDO::PARAM_STR);
        $stmt->bindParam(':phaseId', $phaseId, PDO::PARAM_INT);
        $stmt->execute();
        return $bd->lastInsertId();
    }

    // Fonction pour insérer une entrée dans la table de liaison GOODPRACTICE_PROGRAM
    function InsertGoodpracticeProgram(int $goodpracticeId, int $programId): void
    {
        global $bd;
        $stmt = $bd->prepare("INSERT INTO GOODPRACTICE_PROGRAM (goodpractice_id, program_id) VALUES (:goodpracticeId, :programId)");
        $stmt->bindParam(':goodpracticeId', $goodpracticeId, PDO::PARAM_INT);
        $stmt->bindParam(':programId', $programId, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Fonction pour insérer une entrée dans la table de liaison GOODPRACTICE_KEYWORD
    function InsertGoodpracticeKeyword(int $goodpracticeId, int $keywordId): void
    {
        global $bd;
        $stmt = $bd->prepare("INSERT INTO GOODPRACTICE_KEYWORD (goodpractice_id, keyword_id) VALUES (:goodpracticeId, :keywordId)");
        $stmt->bindParam(':goodpracticeId', $goodpracticeId, PDO::PARAM_INT);
        $stmt->bindParam(':keywordId', $keywordId, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Fonction pour obtenir l'ID de la phase ou insérer une nouvelle phase si elle n'existe pas
    function GetPhaseId(string $phaseName): int
    {
        global $bd;
        $stmt = $bd->prepare("SELECT phase_id FROM PHASE WHERE phase_name = :phaseName");
        $stmt->bindParam(':phaseName', $phaseName, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result !== false) {
            return $result['phase_id'];
        } else {
            // La phase n'existe pas, l'insérer
            return InsertPhase($phaseName);
        }
    }

    // Fonction pour insérer une nouvelle phase et retourner son ID
    function InsertPhase(string $phaseName): int
    {
        global $bd;
        $stmt = $bd->prepare("INSERT INTO PHASE (phase_name) VALUES (:phaseName)");
        $stmt->bindParam(':phaseName', $phaseName, PDO::PARAM_STR);
        $stmt->execute();
        return $bd->lastInsertId();
    }

    // Fonction pour obtenir l'ID du programme ou insérer un nouveau programme s'il n'existe pas
    function GetProgramId(string $programName): int
    {
        global $bd;
        $stmt = $bd->prepare("SELECT program_id FROM PROGRAM WHERE program_name = :programName");
        $stmt->bindParam(':programName', $programName, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result !== false) {
            return $result['program_id'];
        } else {
            // Le programme n'existe pas, l'insérer
            return InsertProgram($programName);
        }
    }

    // Fonction pour insérer un nouveau programme et retourner son ID
    function InsertProgram(string $programName): int
    {
        global $bd;
        $stmt = $bd->prepare("INSERT INTO PROGRAM (program_name) VALUES (:programName)");
        $stmt->bindParam(':programName', $programName, PDO::PARAM_STR);
        $stmt->execute();
        return $bd->lastInsertId();
    }

    // Fonction pour obtenir l'ID du mot-clé ou insérer un nouveau mot-clé s'il n'existe pas
    function GetKeywordId(string $keyword): int
    {
        global $bd;
        $stmt = $bd->prepare("SELECT keyword_id FROM KEYWORD WHERE onekeyword = :keyword");
        $stmt->bindParam(':keyword', $keyword, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result !== false) {
            return $result['keyword_id'];
        } else {
            // Le mot-clé n'existe pas, l'insérer
            return InsertKeyword($keyword);
        }
    }

    // Fonction pour insérer un nouveau mot-clé et retourner son ID
    function InsertKeyword(string $keyword): int
    {
        global $bd;
        $stmt = $bd->prepare("INSERT INTO KEYWORD (onekeyword) VALUES (:keyword)");
        $stmt->bindParam(':keyword', $keyword, PDO::PARAM_STR);
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

    function DeleteGoodpractice(int $goodpracticeId): void
    {

    }
?>