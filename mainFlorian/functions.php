<?php
    function UserIsBlocked(int $attempts): bool { return $userIsBlocked = ($attempts >= 3); }

    function UserAttempts(int $primaryKey, int $option): void 
    {
        /*  Option -->
                If $option === 'reset' : reset login attempts.
                If $option === 'increment' : increment login attempts.
        */

        global $bd;

        $sql = "UPDATE USERS ";
        if ($option === 'reset') {
            $sql .= "SET attempts = 0 ";
        } elseif ($option === 'increment') {
            $sql .= "SET attempts = attempts + 1 ";
        }   
        $sql .= "WHERE userkey = :primarykey";
        $req = $bd->prepare($sql);
        $marqueurs = array('primarykey' => $primaryKey);
        $req->execute($marqueurs) or die(print_r($req->errorInfo()));
        $req->closeCursor();
    }

    function UsersSelect(): array
    {
        global $bd;

        $sql = "select * FROM USERS";
        $req = $bd->prepare($sql);
        $req->execute() or die(print_r($req->errorInfo()));
        $users = $req->fetchall();
        $req->closeCursor();
        return $users;
    }

    function PasswordSelect()
    {
        global $bd;

        $sql = "select * FROM PASSWORD";
        $req = $bd->prepare($sql);
        $req->execute() or die(print_r($req->errorInfo()));
        $passwordParameters = $req->fetchall();
        $req->closeCursor();
        return $passwordParameters;
    }
        
?>
