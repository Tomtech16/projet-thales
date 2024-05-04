<?php
    function UserIsBlocked(int $attempts): bool { return $userIsBlocked = ($attempts >= 3); }

    function IncrementUserAttempts(int $primaryKey): void 
    {
        global $bd;

        $sql = "UPDATE USERS ";
        $sql .= "SET attempts = attempts + 1 ";
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
