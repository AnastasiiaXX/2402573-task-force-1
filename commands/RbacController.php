<?php

namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
  public function actionInit()
  {
    $auth = Yii::$app->authManager;

    $customer = $auth->createRole('customer');
    $worker = $auth->createRole('worker');

    $auth->add($customer);
    $auth->add($worker);

    echo "Roles created\n";
  }

  public function actionAssign($userId, $role)
  {
    $auth = Yii::$app->authManager;

    $roleObj = $auth->getRole($role);
    if (!$roleObj) {
      echo "Role not found\n";
      return;
    }

    $auth->assign($roleObj, $userId);
    echo "Role assigned\n";
  }
}
