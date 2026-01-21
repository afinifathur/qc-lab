<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder {
  public function run(): void {
    foreach (['Operator','Approver','Auditor'] as $r) {
      Role::findOrCreate($r);
    }
  }
}
