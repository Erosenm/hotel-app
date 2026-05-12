<?php
class Usuario
{
    private $conn;

    // El constructor recibe la conexión PDO
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Obtener todos los usuarios
    public function all()
    {
        $stmt = $this->conn->query("SELECT * FROM usuarios");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Crear usuario
    public function create($data)
    {
        $sql = "INSERT INTO usuarios (codigo,nombre,paterno,materno,ci, email, password) VALUES (:codigo,:nombre,:paterno,:materno,:ci, :email, :password)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    // Eliminar usuario
    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM usuarios WHERE idusuarios = ?");
        return $stmt->execute([$id]);
    }           
}
