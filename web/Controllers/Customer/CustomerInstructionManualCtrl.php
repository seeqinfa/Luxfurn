<?php
// Src/Controllers/CustomerInstructionManualCtrl.php
require_once dirname(__DIR__,2) . '/Entities/Instruction_Manuals.php';

class CustomerInstructionManualCtrl
{
    public function list(string $q = '', int $page = 1, int $perPage = 10): array
    {
        return instruction_manuals::search($q, $page, $perPage);
    }
}