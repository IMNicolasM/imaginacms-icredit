<?php

namespace Modules\Icredit\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Icommerce\Entities\PaymentMethod;
use Modules\Isite\Jobs\ProcessSeeds;

class IcreditDatabaseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Model::unguard();
    ProcessSeeds::dispatch([
      "baseClass" => "\Modules\Icredit\Database\Seeders",
      "seeds" => ["IcreditModuleTableSeeder", "WithdrawalfundsFormTableSeeder"]
    ]);


    $name = config('asgard.icredit.config.paymentName');
    $result = PaymentMethod::where('name', $name)->first();

    if (!$result) {

      $options['init'] = "Modules\Icredit\Http\Controllers\Api\PaymentApiController";
      $options['minimunAmount'] = 0;

      $titleTrans = 'icredit::credits.title.credits';
      $descriptionTrans = 'icredit::credits.description';

      $params = array(
        'name' => $name,
        'status' => 1,
        'options' => $options
      );
      $paymentMethod = PaymentMethod::create($params);

      $this->addTranslation($paymentMethod, 'en', $titleTrans, $descriptionTrans);
      $this->addTranslation($paymentMethod, 'es', $titleTrans, $descriptionTrans);

    } else {

      $this->command->alert("This method has already been installed !!");

    }

  }

  /*
  * Add Translations
  * PD: New Alternative method due to problems with astronomic translatable
  **/
  public function addTranslation($paymentMethod, $locale, $title, $description)
  {

    \DB::table('icommerce__payment_method_translations')->insert([
      'title' => trans($title, [], $locale),
      'description' => trans($description, [], $locale),
      'payment_method_id' => $paymentMethod->id,
      'locale' => $locale
    ]);

  }
}