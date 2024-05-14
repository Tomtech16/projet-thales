<?php    

    function PasswordSelect(): array
    {
        // return data of PASSWORD table

        global $bd;

        $sql = "select * from PASSWORD";
        $req = $bd->prepare($sql);
        $req->execute() or die(print_r($req->errorInfo()));
        $passwordParameters = $req->fetchall();
        $req->closeCursor();
        foreach ($passwordParameters as $parameters) {
            return $parameters;
        }
    }

    function UsersSelect(): array
    {
        // return data of USERS table

        global $bd;

        $sql = "select * from USERS";
        $req = $bd->prepare($sql);
        $req->execute() or die(print_r($req->errorInfo()));
        $users = $req->fetchall();
        $req->closeCursor();
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
        $req = $bd->prepare($sql);
        $marqueurs = array('primarykey' => $primaryKey);
        $req->execute($marqueurs) or die(print_r($req->errorInfo()));
        $req->closeCursor();
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
            $req = $bd->prepare($sql);
            $marqueurs = array('username' => $username, 'firstname' => $firstname, 'lastname' => $lastname, 'password' => $hash);
            $req->execute($marqueurs) or die(print_r($req->errorInfo()));
            $req->closeCursor();
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function GoodPracticesSelect(array $whereIs = NULL, array $orderBy = NULL): array
    {
        // Return data of tables :
        //     - PROGRAM(program_names)
        //     - PHASE(phase_name)
        //     - GOODPRACTICE(item)
        //     - KEYWORD(keywords)
            
        // Options :
        //     - $whereIs = array($column => $value)

        //         -- $column :
        //             - 'program_names'
        //             - 'phase_name'
        //             - 'item'
        //             - 'keywords'

        //         -- $value :
        //             - The filter to apply.
                    
        //         ==> WHERE $column = $value
            
        //     - $orderBy = array($column)
                
        //         ==> ORDER BY $column

        global $bd;
    
        $sql = "
            SELECT 
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
        
        $sql .= ' GROUP BY GOODPRACTICE.item';
    
        // Check for ORDER BY clause
        if ($orderBy != NULL) {
            $orderClause = " ORDER BY ";
        
            foreach ($orderBy as $by) {
                if ($by === 'program_names' || $by === 'phase_name' || $by === 'item' || $by === 'keywords') {
                    $orderClause .= "$by, ";
                }
            }
            
            // Remove the last comma if it exists
            $orderClause = rtrim($orderClause, ', ');
            $sql .= $orderClause;
        }
    
        $sql .= ";";
        
        // Prepare and execute the SQL query
        $req = $bd->prepare($sql);
    
        // Bind values to parameters in WHERE clause
        if ($whereIs != NULL) {
            foreach ($whereIs as $where => $is) {
                $req->bindParam(":$where", $is);
            }
        }
    
        // For tests
        print_r($sql);
    
        $req->execute() or die(print_r($req->errorInfo()));
        $goodPractices = $req->fetchAll();
        $req->closeCursor();
        return $goodPractices;
    }

?>