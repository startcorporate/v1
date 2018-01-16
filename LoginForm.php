<?php

namespace common\models;

use Yii;
use yii\base\Model;
use frontend\api\Biztositok;

/**
 * Login form
 */
class LoginForm extends Model
{

    public $email;
    public $password;
    public $rememberMe = true;
    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['email', 'password'], 'required', 'message' => 'A mező kitöltése kötelező'],
            ['email', 'email', 'message' => 'Hibás e-mail cím!'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
        ];
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        if (parent::validate($attributeNames, $clearErrors)) {
            try {
                $response = Biztositok::callApi('user/login', [
                            'email' => trim($this->email),
                            'password' => trim($this->password),
                ]);
                
                Yii::$app->biztositokUser->initBiztositokSession($response->get('sessionid'));
                
            } catch (\frontend\api\BiztositokAPIException $e) {

                $errors = Biztositok::getLastApiResponse()->getErrors();
                foreach ($errors as $error) {
                    $this->addError($error["field"], $error['error_message']);
                }
                return false;
                
            } catch (\yii\base\Exception $e) {
                $this->addError("email", "Rendszerhiba!");
                return false;
            }
        }

        return true;
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     * @throws BiztositokAPIException
     */
    public function login()
    {
        
        
        return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername("demo");
        }

        return $this->_user;
    }

}
