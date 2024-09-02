<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Models\Checklist;
use App\Models\QuestionType;
use App\Models\Type;
use App\Models\Absences;
use App\Models\VacationDays;
use App\Models\TruckBrand;
use App\Models\TruckModel;
use App\Models\TransportType;
use App\Models\Status;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@hovo.pt',
            'phone' => '912345678',
            'password' => Hash::make('admin@22'),
            'vac_days' => '2',
        ]);

        $gestor = User::create([
            'name' => 'Gestor',
            'email' => 'gestor@hovo.pt',
            'phone' => '961497169',
            'password' => Hash::make('testeteste'),
            'vac_days' => '10',
        ]);

        $cliente = User::create([
            'name' => 'Cliente',
            'email' => 'cliente@hovo.pt',
            'phone' => '961235212',
            'password' => Hash::make('testeteste'),
            'vac_days' => '7',
        ]);

        $vac_days1 = VacationDays::create([
            'user_id' => '1',
            'num_days' => '12',
            'year'=> '2023',
        ]);

        $vac_days2 = VacationDays::create([
            'user_id' => '2',
            'num_days' => '22',
            'year' => '2023',
        ]);

        $vac_days3 = VacationDays::create([
            'user_id'=> '3',
            'num_days'=> '19',
            'year'=>'2023',
        ]);
        
        $vac_days4 = VacationDays::create([
            'user_id'=> '1',
            'num_days'=> '19',
            'year'=>'2022',
        ]);

        $perguntaCurta = QuestionType::create([
            'name' => 'Resposta Curta',
        ]);

        $perguntaLonga = QuestionType::create([
            'name' => 'Parágrafo',
        ]);

        $perguntaEscolhaMultipla = QuestionType::create([
            'name' => 'Escolha Múltipla',
        ]);

        $checklist = Checklist::create([
            'name' => 'Checklist Múltipla',
            'created_by' => 1,
        ]);

        // $questao = Question::create([
        //     'description' => 'Escolha Múltipla',
        //     'checklist_id' => 1,
        //     'type' => 1,
        //     'mandatory' => 1,
        // ]);
        
        $type1 = Type::create([
            'name' => 'Férias',
            'has_hours' => true,
            'color' => '#ffb800',
            'status'=> 1,
        ]);

        $type2 = Type::create([
            'name'=> 'Baixas',
            'has_hours' => true,
            'color' => '#ff6f00',
            'status'=> 1,
        ]);
        $type3 = Type::create([
            'name'=> 'Faltas Justificada',
            'has_hours' => true,
            'color' => '#00c800',
            'status'=> 1, 
        ]);
        $type4 = Type::create([
            'name'=> 'Faltas Injustificada',
            'has_hours' => true,
            'color' => '#d90000',
            'status'=> 1, 
        ]);
        $type5 = Type::create([
            'name'=> 'Licenças',
            'has_hours' => true,
            'color' => '#ff7eeb',
            'status'=> 1, 
        ]);
        $type6 = Type::create([
            'name'=> 'Acidentes',
            'has_hours' => true,
            'color' => '#00a0f3',
            'status'=> 1, 
        ]);
        $type7 = Type::create([
            'name'=> 'Compensação Feriado',
            'has_hours' => true,
            'color' => '#7000fe',
            'status'=> 1, 
        ]);
        $type8 = Type::create([
            'name'=> 'Luto',
            'has_hours' => true,
            'color' => '#808080',
            'status'=> 1, 
        ]);

        $absence1 = Absences::create([
            'start_date' => '2023-01-15',
            'end_date' => '2023-01-17',
            'user_id' => $admin->id,
            'type' => $type1->id,
            'created_by' => 1,
            'confirmed' => true,
        ]);

        $Ford = TruckBrand::create([
            'brand' => 'Ford',
            
        ]);

        $Scania = TruckBrand::create([
            'brand' => 'Scania',
            
        ]);

        $Model1 = TruckModel::create([
            'brand_id' => '2',
            'model' => 'Series G',
            
        ]);

        $Model2 = TruckModel::create([
            'brand_id' => '2',
            'model' => 'Series L',
            
        ]);

        $Model3 = TruckModel::create([
            'brand_id' => '1',
            'model' => 'F-MAX',
            
        ]);

        $type1 = TransportType::create([
            'type' => 'Semi-Reboque TIR',
            
        ]);
        $type2 = TransportType::create([
            'type' => 'Transporte Especial',
            
        ]);
        $type3= TransportType::create([
            'type' => 'Serviço de Grua',
            
        ]);



        $adminRole = Role::findByName(\App\Extra\Acl::ROLE_ADMIN);
        $gestorRole = Role::findByName(\App\Extra\Acl::ROLE_MANAGER);
        $clienteRole = Role::findByName(\App\Extra\Acl::ROLE_USER);

        $admin->syncRoles($adminRole);
        $gestor->syncRoles($gestorRole);
        $cliente->syncRoles($clienteRole);
    }
}
