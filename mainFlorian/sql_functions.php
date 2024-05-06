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
?>