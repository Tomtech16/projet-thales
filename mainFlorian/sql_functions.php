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

        $params = array(); // Array to store parameter values

        // Check for WHERE clause
        if ($whereIs != NULL) {
            $whereClause = " WHERE ";

            foreach ($whereIs as $column => $filters) {
                foreach ($filters as $index => $value) {
                    if (!empty($value)) {
                        $paramName = ":$column$index"; // Unique parameter name
                        $whereClause .= "$column = $paramName OR ";
                        $params[$paramName] = $value; // Store parameter value
                    }
                }
                $whereClause = replaceLastOccurrence($whereClause, 'OR', 'AND');
            }
            if ($whereClause != " WHERE " && $whereClause != " WHERE  AND " && $whereClause != " WHERE" && $whereClause != "WHERE") {
                $whereClause = rtrim($whereClause, ' AND ');
                $sql .= $whereClause;
            }
        }

        // Check for excluded good practices
        if ($deletedGoodpractices != NULL) {
            // Construct the IN clause for excluded goodpractice_ids
            $excludedIds = implode(", ", $deletedGoodpractices);
            $sql .= " AND GOODPRACTICE.goodpractice_id NOT IN ($excludedIds)";
        }

        $sql .= ' GROUP BY GOODPRACTICE.item';

        // Check for ORDER BY clause
        if ($orderBy != NULL) {
            $orderClause = " ORDER BY ";
            $oneBy = FALSE;

            foreach ($orderBy as $by => $dir) {
                if ($by === 'program_names' || $by === 'phase_name' || $by === 'item' || $by === 'keywords') {
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
        print_r($sql);
        // Prepare and execute the SQL query
        $stmt = $bd->prepare($sql);

        // Bind values to parameters
        foreach ($params as $paramName => $value) {
            $stmt->bindValue($paramName, $value);
        }

        $stmt->execute() or die(print_r($stmt->errorInfo()));
        $goodPractices = $stmt->fetchAll();
        $stmt->closeCursor();
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


    function DeleteGoodpractice(int $goodpracticeId): void
    {

    }
?>