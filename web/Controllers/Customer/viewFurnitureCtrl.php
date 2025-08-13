<?php
require_once dirname(__DIR__, 2) . '/Entities/Furniture.php';

class FurnitureController
{
    public function countFurniture(string $searchTerm = ''): int
    {
        return Furniture::count($searchTerm);
    }

    public function getFurniturePaginated(int $offset, int $limit, string $searchTerm = ''): array
    {
        return Furniture::findPaginated($offset, $limit, $searchTerm);
    }

    public function getFurnitureById(int $id): ?Furniture
    {
        return Furniture::findById($id);
    }
}
