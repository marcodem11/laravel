<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Item;

class ItemsCreateCommand extends Command
{
    /**
     * Nome e firma del comando.
     */
    protected $signature = 'items:create 
                            {name : Nome dell\'item} 
                            {--category= : Nome della categoria} 
                            {--qty=1 : Quantità disponibile} 
                            {--status=available : Stato (available|unavailable)} 
                            {--desc= : Descrizione opzionale}';

    /**
     * Descrizione del comando.
     */
    protected $description = 'Crea un nuovo item nell\'inventario';

    /**
     * Esecuzione comando.
     */
    public function handle()
    {
        $name     = $this->argument('name');
        $categoryName = $this->option('category');
        $qty      = (int) $this->option('qty');
        $status   = $this->option('status');
        $desc     = $this->option('desc');

        // Trova o crea categoria
        $category = null;
        if ($categoryName) {
            $category = Category::firstOrCreate(['name' => $categoryName]);
        }

        // Crea l'item
        $item = Item::create([
            'name'        => $name,
            'category_id' => $category?->id,
            'quantity'    => $qty,
            'status'      => $status,
            'description' => $desc,
        ]);

        $this->info("✅ Item creato: {$item->name} (Qty: {$item->quantity}, Status: {$item->status})");

        return Command::SUCCESS;
    }
}