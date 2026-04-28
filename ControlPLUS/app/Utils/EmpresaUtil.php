<?php

namespace App\Utils;

use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Produto;
use App\Models\Empresa;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Localizacao;
use App\Models\ProdutoLocalizacao;
use App\Models\UsuarioLocalizacao;
use App\Models\NaturezaOperacaoSuper;
use App\Models\NaturezaOperacao;
use App\Models\PadraoTributacaoProdutoSuper;
use App\Models\PadraoTributacaoProduto;
use Illuminate\Support\Facades\Artisan;

class EmpresaUtil
{
    private const LOCAL_PADRAO_DESCRICAO = 'PADRÃO';

    public function defaultPermissions($empresa_id)
    {
        $empresa = Empresa::findOrFail($empresa_id);
        $usuarios = $empresa->usuarios;

        $roles = Role::where('empresa_id', null)->get();

        // if(sizeof($roles) == 0){
        // 	$roles = Role::where('empresa_id', '!=', null)
        // 	->limit(1)
        // 	->get();
        // }

        Artisan::call('cache:forget spatie.permission.cache');
        foreach ($roles as $role) {

            if ($role->name != 'gestor_plataforma') {

                foreach ($usuarios as $u) {
                    $user = $u->usuario;
                    $r = Role::create([
                        'name' => $role->description . '#' . $empresa_id,
                        'description' => $role->description,
                        'empresa_id' => $empresa_id,
                        'guard_name' => 'web',
                        'is_default' => 1,
                        'type_user' => 2
                    ]);
                    $permissions = [];
                    foreach ($role->permissions as $p) {
                        array_push(
                            $permissions,
                            [
                                'permission_id' => $p->id,
                                'role_id' => $r->id,
                            ]
                        );
                    }

                    $role->permissions()->attach($permissions);
                    $user->assignRole($r->name);
                }
            }
        }

        $this->syncCompanyPermissions($empresa_id);
    }

    public function syncCompanyPermissions($empresa_id)
    {
        $baseAdmin = Role::where('empresa_id', null)
            ->where('name', 'admin')
            ->first();

        if (!$baseAdmin) {
            return;
        }

        $baseAdmin->permissions()->sync(Permission::all());

        $companyRoles = Role::where('empresa_id', $empresa_id)
            ->where('description', $baseAdmin->description)
            ->get();

        if ($companyRoles->count() == 0) {
            Artisan::call('cache:forget spatie.permission.cache');
            return;
        }

        $permissionIds = $baseAdmin->permissions()->pluck('permissions.id')->all();
        foreach ($companyRoles as $companyRole) {
            $companyRole->permissions()->sync($permissionIds);
        }

        Artisan::call('cache:forget spatie.permission.cache');
    }

    public function getPermissions($empresa_id)
    {
        $empresa = Empresa::findOrFail($empresa_id);
        $user = $empresa->usuarios[0]->usuario;

        return $user->getAllPermissions();
    }

    public function createPermissions()
    {
        $count = Permission::count();
        if ($count == 0) {
            $this->createPermissionsDefault();
        }

        $count = Role::count();
        if ($count == 0) {
            $this->createRolesDefault();
        }
    }

    private function createPermissionsDefault()
    {
        // Seed the default permissions
        $permissions = Permission::defaultPermissions();

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }

    private function createRolesDefault()
    {
        $superadmin = Role::firstOrCreate([
            'name' => 'gestor_plataforma'
        ], [
            'description' => 'Gestor Plataforma',
            'type_user' => 1
        ]);
        $superadmin->permissions()->sync(Permission::all());

        $admin = Role::firstOrCreate([
            'name' => 'admin',
        ], [
            'description' => 'Admin',
            'type_user' => 2
        ]);
        $admin->permissions()->sync(Permission::all());
    }

    public function initLocation($empresa)
    {
        $defaultJaExistia = $this->getDefaultLocationByDescricao($empresa->id) != null;
        $localizacao = $this->ensureDefaultLocation($empresa);

        if (!$defaultJaExistia) {
            foreach ($empresa->usuarios as $u) {
                UsuarioLocalizacao::updateOrCreate([
                    'usuario_id' => $u->usuario_id,
                    'localizacao_id' => $localizacao->id
                ]);
            }
        }

        $this->initProducts($empresa->id, $localizacao->id);
        $this->initRegisters($empresa->id, $localizacao->id);
    }

    public function initNaturezaTributacao($empresa)
    {

        $data = NaturezaOperacaoSuper::where('status', 1)->get();
        foreach ($data as $item) {
            $obj = $item->toArray();
            $obj['empresa_id'] = $empresa->id;
            NaturezaOperacao::create($obj);
        }

        $data = PadraoTributacaoProdutoSuper::where('status', 1)->get();
        foreach ($data as $item) {
            $obj = $item->toArray();
            $obj['empresa_id'] = $empresa->id;
            PadraoTributacaoProduto::create($obj);
        }
    }

    private function initProducts($empresa_id, $localizacaoPadraoId = null)
    {
        $produtos = Produto::where('empresa_id', $empresa_id)->get();
        $localizacao = $localizacaoPadraoId
            ? Localizacao::find($localizacaoPadraoId)
            : $this->getDefaultLocation($empresa_id);
        if ($localizacao) {
            foreach ($produtos as $p) {
                $produtoLocalizacao = ProdutoLocalizacao::where('produto_id', $p->id)->first();
                if ($produtoLocalizacao == null) {
                    ProdutoLocalizacao::updateOrCreate([
                        'produto_id' => $p->id,
                        'localizacao_id' => $localizacao->id
                    ]);
                }
            }
        }
    }

    private function initRegisters($empresa_id, $localizacaoPadraoId = null)
    {
        $localizacao = $localizacaoPadraoId
            ? Localizacao::find($localizacaoPadraoId)
            : $this->getDefaultLocation($empresa_id);

        if (!$localizacao) {
            return;
        }

        \App\Models\Nfe::where('empresa_id', $empresa_id)->where('local_id', null)
            ->update(['local_id' => $localizacao->id]);

        \App\Models\Nfce::where('empresa_id', $empresa_id)->where('local_id', null)
            ->update(['local_id' => $localizacao->id]);

        \App\Models\Cte::where('empresa_id', $empresa_id)->where('local_id', null)
            ->update(['local_id' => $localizacao->id]);

        \App\Models\Mdfe::where('empresa_id', $empresa_id)->where('local_id', null)
            ->update(['local_id' => $localizacao->id]);

        \App\Models\ContaPagar::where('empresa_id', $empresa_id)->where('local_id', null)
            ->update(['local_id' => $localizacao->id]);

        \App\Models\ContaReceber::where('empresa_id', $empresa_id)->where('local_id', null)
            ->update(['local_id' => $localizacao->id]);
    }

    public function initUserLocations($user)
    {
        if ($user->empresa && sizeof($user->locais) == 0) {
            $empresa_id = $user->empresa->empresa_id;
            $localizacao = $this->getDefaultLocation($empresa_id);
            if (!$localizacao) {
                return;
            }
            UsuarioLocalizacao::updateOrCreate([
                'usuario_id' => $user->id,
                'localizacao_id' => $localizacao->id
            ]);
        }
    }

    private function ensureDefaultLocation($empresa)
    {
        $localizacao = $this->getDefaultLocationByDescricao($empresa->id);
        if ($localizacao) {
            if ((int)$localizacao->status !== 1) {
                $localizacao->status = 1;
                $localizacao->save();
            }
            return $localizacao;
        }

        $legacyDefault = $this->getLegacyDefaultLocation($empresa->id);

        if ($legacyDefault) {
            $legacyDefault->descricao = self::LOCAL_PADRAO_DESCRICAO;
            $legacyDefault->status = 1;
            $legacyDefault->save();
            return $legacyDefault;
        }

        $localizacao = $empresa->toArray();
        $localizacao['descricao'] = self::LOCAL_PADRAO_DESCRICAO;
        $localizacao['empresa_id'] = $empresa->id;
        $localizacao['status'] = 1;
        $localizacao['logo'] = $localizacao['logo'] ?? '';
        $localizacao['cpf_cnpj'] = $localizacao['cpf_cnpj'] ?? str_pad((string)$empresa->id, 14, '0', STR_PAD_LEFT);
        $localizacao['ambiente'] = $localizacao['ambiente'] ?? 2;
        $localizacao['tributacao'] = match ($empresa->tributacao) {
            'MEI' => 'MEI',
            'Simples Nacional' => 'Simples Nacional',
            'Simples Nacional, excesso sublimite de receita bruta' => 'Simples Nacional',
            'Regime Normal' => 'Regime Normal',
            default => 'Regime Normal',
        };

        return Localizacao::create($localizacao);
    }

    private function getDefaultLocationByDescricao($empresa_id)
    {
        return Localizacao::where('empresa_id', $empresa_id)
            ->whereRaw('BINARY TRIM(descricao) = ?', [self::LOCAL_PADRAO_DESCRICAO])
            ->orderBy('id')
            ->first();
    }

    private function getDefaultLocation($empresa_id)
    {
        $localizacao = $this->getDefaultLocationByDescricao($empresa_id);
        if ($localizacao) {
            return $localizacao;
        }

        $localizacaoLegada = $this->getLegacyDefaultLocation($empresa_id);
        if ($localizacaoLegada) {
            return $localizacaoLegada;
        }

        $localizacaoAtiva = Localizacao::where('empresa_id', $empresa_id)
            ->where('status', 1)
            ->orderBy('id')
            ->first();

        if ($localizacaoAtiva) {
            return $localizacaoAtiva;
        }

        return Localizacao::where('empresa_id', $empresa_id)
            ->orderBy('id')
            ->first();
    }

    private function getLegacyDefaultLocation($empresa_id)
    {
        return Localizacao::where('empresa_id', $empresa_id)
            ->where(function ($query) {
                $query->whereRaw('UPPER(TRIM(descricao)) = ?', ['BL0001'])
                    ->orWhereRaw('UPPER(TRIM(descricao)) = ?', ['PADRAO'])
                    ->orWhereRaw('UPPER(TRIM(descricao)) LIKE ?', ['LOCAL DE ARMAZENAMENTO%'])
                    ->orWhereRaw('UPPER(TRIM(descricao)) LIKE ?', ['BL000%']);
            })
            ->orderBy('id')
            ->first();
    }
}
