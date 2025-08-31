<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Console\Command;

class ItemsCreateCommand extends Command
{
    protected $signature = 'items:create
        {--name= : Nome dell\'item}
        {--category= : ID o nome categoria}
        {--quantity=1 : Quantità iniziale}
        {--status=available : available|unavailable}
        {--description= : Descrizione opzionale}';

    protected $description = 'Crea rapidamente un item di inventario';

    public function handle(): int
    {
        $name = (string) $this->option('name');
        $cat  = $this->option('category');
        $qty  = (int) $this->option('quantity');
        $status = (string) $this->option('status');
        $desc = $this->option('description');

        if ($name === '' || $cat === null) {
            $this->error(' --name e --category sono obbligatori');
            return self::FAILURE;
        }

        // categoria: accetta ID o nome
        $category = is_numeric($cat)
            ? Category::find((int)$cat)
            : Category::firstOrCreate(['name' => (string)$cat]);

        if (!$category) {
            $this->error('Categoria non trovata');
            return self::FAILURE;
        }

        $item = Item::create([
            'name'        => $name,
            'category_id' => $category->id,
            'quantity'    => $qty,
            'status'      => $status,
            'description' => $desc,
        ]);

        $this->info("Creato item #{$item->id} {$item->name} ({$category->name})");
        return self::SUCCESS;
    }
}