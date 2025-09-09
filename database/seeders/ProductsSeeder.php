<?php

// database/seeders/ProductsSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder {
  public function run(): void {
    DB::table('products')->insert([
      // refeições — meia para 7–12
      ['sku'=>'REF_SAB_ALMOCO','name'=>'Almoço Sábado','price'=>3500,'is_child_half'=>true,'active'=>true,'created_at'=>now(),'updated_at'=>now()],
      ['sku'=>'REF_DOM_ALMOCO','name'=>'Almoço Domingo','price'=>3500,'is_child_half'=>true,'active'=>true,'created_at'=>now(),'updated_at'=>now()],
      // passeio turístico — R$ 30 (todas as idades; criança que ocupa assento paga)
      ['sku'=>'PAS_TUR','name'=>'Passeio Turístico','price'=>3000,'is_child_half'=>false,'active'=>true,'created_at'=>now(),'updated_at'=>now()],
      // camisetas (exemplo)
      ['sku'=>'CAM_S','name'=>'Camiseta Tam. S','price'=>4500,'is_child_half'=>false,'active'=>true,'created_at'=>now(),'updated_at'=>now()],
      ['sku'=>'CAM_M','name'=>'Camiseta Tam. M','price'=>4500,'is_child_half'=>false,'active'=>true,'created_at'=>now(),'updated_at'=>now()],
      ['sku'=>'CAM_G','name'=>'Camiseta Tam. G','price'=>4500,'is_child_half'=>false,'active'=>true,'created_at'=>now(),'updated_at'=>now()],
      ['sku'=>'CAM_XG','name'=>'Camiseta Tam. XG','price'=>4500,'is_child_half'=>false,'active'=>true,'created_at'=>now(),'updated_at'=>now()],
    ]);
  }
}
