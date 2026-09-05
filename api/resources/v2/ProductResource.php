<?php

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../models/Product.php";

class ProductResource
{
    private $db;
    private $product;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->product = new Product($this->db);
    }

  
    public function index()
    {
        $stmt = $this->product->read();

        $products = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $products[] = $row;
        }

        http_response_code(200);

        echo json_encode([
            "success" => true,
            "data" => $products
        ]);
    }

    // GET
    public function show($id)
    {
        $this->product->id = $id;

        if ($this->product->readOne()) {

            http_response_code(200);

            echo json_encode([
                "success" => true,
                "data" => [
                    "id" => $this->product->id,
                    "sku" => $this->product->sku,
                    "name" => $this->product->name,
                    "description" => $this->product->description,
                    "price" => $this->product->price,
                    "stock" => $this->product->stock,
                    "created_at" => $this->product->created_at,
                    "updated_at" => $this->product->updated_at
                ]
            ]);

        } else {

            http_response_code(404);

            echo json_encode([
                "success" => false,
                "message" => "Producto no encontrado"
            ]);
        }
    }

    // POST
    public function store()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (
            empty($data->sku) ||
            empty($data->name) ||
            !isset($data->price)
        ) {
            http_response_code(400);

            echo json_encode([
                "success" => false,
                "message" => "SKU, nombre y precio son obligatorios"
            ]);

            return;
        }

        $this->product->sku = $data->sku;
        $this->product->name = $data->name;
        $this->product->description = $data->description ?? null;
        $this->product->price = $data->price;
        $this->product->stock = $data->stock ?? 0;

        if ($this->product->create()) {

            http_response_code(201);

            echo json_encode([
                "success" => true,
                "message" => "Producto creado correctamente",
                "id" => $this->product->id
            ]);

        } else {

            http_response_code(500);

            echo json_encode([
                "success" => false,
                "message" => "No se pudo crear el producto"
            ]);
        }
    }

    // PUT
    public function update($id)
    {
        $data = json_decode(file_get_contents("php://input"));

        if (
            empty($data->sku) ||
            empty($data->name) ||
            !isset($data->price)
        ) {
            http_response_code(400);

            echo json_encode([
                "success" => false,
                "message" => "SKU, nombre y precio son obligatorios"
            ]);

            return;
        }

        $this->product->id = $id;
        $this->product->sku = $data->sku;
        $this->product->name = $data->name;
        $this->product->description = $data->description ?? null;
        $this->product->price = $data->price;
        $this->product->stock = $data->stock ?? 0;

        if ($this->product->update()) {

            http_response_code(200);

            echo json_encode([
                "success" => true,
                "message" => "Producto actualizado correctamente"
            ]);

        } else {

            http_response_code(500);

            echo json_encode([
                "success" => false,
                "message" => "No se pudo actualizar el producto"
            ]);
        }
    }

    // DELETE
    public function destroy($id)
    {
        $this->product->id = $id;

        if ($this->product->delete()) {

            http_response_code(200);

            echo json_encode([
                "success" => true,
                "message" => "Producto eliminado correctamente"
            ]);

        } else {

            http_response_code(500);

            echo json_encode([
                "success" => false,
                "message" => "No se pudo eliminar el producto"
            ]);
        }
    }
}
?>
