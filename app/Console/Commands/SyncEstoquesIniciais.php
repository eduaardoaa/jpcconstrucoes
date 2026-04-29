<?php

namespace App\Console\Commands;

use App\Services\EstoqueSyncService;
use Illuminate\Console\Command;

class SyncEstoquesIniciais extends Command
{
    protected $signature = 'estoque:sync-inicial';
    protected $description = 'Cria registros de estoque zerado no estoque central e em todas as obras para produtos e variações existentes';

    public function handle(EstoqueSyncService $estoqueSyncService): int
    {
        $this->info('Sincronizando estoques iniciais do central e das obras...');

        $estoqueSyncService->syncAll();

        $this->info('Estoques sincronizados com sucesso.');

        return self::SUCCESS;
    }
}