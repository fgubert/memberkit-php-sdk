<?php 

namespace MemberKit;

use GuzzleHttp\Client as GClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class Client {
	 /**
     * @var string
     */
	const MK_VERSION = '0.1';

	 /**
     * @var string
     */
	const BASE_URI = 'https://memberkit.com.br/api/v1/';

	/**
     * @var string
     */
	private $api_key;
	
	/**
     * @var \GuzzleHttp\Client
     */
	private $http;

	/**
     * @var array
     */
	private $options = [];
	
	/**
     * @var array
     */
	private $form_params = [];

	/**
     * @var array
     */
	private $return_msg = ['code'=>null, 'message'=>null, 'status'=>false];

	/**
     * @param string $api_key
     */
	public function __construct($api_key)
	{
		$this->api_key = $api_key;

		$this->options = [
						'timeout' => 2, 
						'http_errors' => false, 
						'headers'=>['User-Agent' => 'checkout-topinvest/1.0']
					   ];

		$this->http = new GClient($this->options);
	}

	 /**
     * @param array $form_params
     *
     * @return array
     */
	protected function getOptions($form_params=null) {
		if (!empty($form_params)) $this->options['form_params'] = $form_params;
		return $this->options;
	}

	/**
     * @param string $method
     * @param string $type
     * @param int $id
     *
     * @return string
     */
	protected function getURL($method='POST', $type, $id=null) {
		$url = null;
		switch ($type) {
			case 'newUser':
				$url = self::BASE_URI.'users';
				break;

			case 'membership_levels':
				$url = self::BASE_URI.'membership_levels';
				break;

			case 'classrooms':
				$url = self::BASE_URI.'classrooms';
				break;

			case 'rankings':
				$url = self::BASE_URI.'rankings';
				break;

			case 'user_ranking':
				$url = self::BASE_URI.'users/'.$id.'/rankings';
				break;

            case 'user_activities':
                $url = self::BASE_URI.'users/'.$id.'/activities';
                break;

            case 'courses':
                $url = self::BASE_URI.'courses';
                break;

            case 'course':
                $url = self::BASE_URI.'courses/'.$id;
                break;

			case 'token':
				$url = self::BASE_URI.'tokens';
				break;

			case 'scores':
				$url = self::BASE_URI.'scores';
				break;

			case 'delete_scores':
				$url = self::BASE_URI.'scores';
				break;

			case 'delete_lesson_statuses':
				$url = self::BASE_URI.'lesson_statuses';
				break;

			
			
			default:
				return false;
				break;
		}
		if ($method == 'GET') $url .= '?api_key='.$this->api_key;

		return $url;
	}

	/**
     * @param string $full_name
     * @param string $email
     * @param string $status
     * @param boolean $blocked
     * @param array $classroom_ids
     * @param boolean $unlimited
     * @param int $membership_level_id
     * @param date $expires_at
     *
     * @return array
     */
	public function newUser($full_name, $email, $status='active', $blocked=false, $classroom_ids=array(), $unlimited=false, $membership_level_id=null, $expires_at=null) {
		$this->form_params = [
			'full_name'=>$full_name,
			'email'=>$email,
			'status'=>$status,
			'blocked'=>$blocked,
			'api_key'=>$this->api_key,
		];
		
		if (!empty($classroom_ids)) $this->form_params['classroom_ids'] = implode(',', $classroom_ids);
		if (!empty($membership_level_id)) $this->form_params['membership_level_id'] = $membership_level_id;
		if (!empty($expires_at)) $this->form_params['expires_at'] = $expires_at;
		if (!empty($unlimited)) $this->form_params['unlimited'] = $unlimited;

		$request = new Request('POST', $this->getURL('POST', 'newUser'), $this->getOptions($this->form_params), null);

		$response = $this->http->send($request, $this->getOptions($this->form_params), null);

		$code = $response->getStatusCode();
		$body = (string)$response->getBody();

		$this->return_msg['code'] = $code;
		
		if ($code == 201) {
			$this->return_msg['message'] = 'success';
			$this->return_msg['status'] = true;
		} else {
			$this->return_msg['message'] = json_decode($body);
		}
		return $this->return_msg;
	}

	/**
     * @param string $email
     *
     * @return array
     */
	public function token($email) {
		$this->form_params = [
			'email'=>$email,
			'api_key'=>$this->api_key,
		];
		
		$request = new Request('POST', $this->getURL('POST', 'token'), $this->getOptions($this->form_params), null);

		$response = $this->http->send($request, $this->getOptions($this->form_params), null);

		$code = $response->getStatusCode();
		$body = (string)$response->getBody();

		$this->return_msg['code'] = $code;
		
		if ($code == 201) {
			$this->return_msg['message'] = 'success';
			$this->return_msg['content'] = json_decode($body);
			$this->return_msg['status'] = true;
		} else {
			$this->return_msg['message'] = json_decode($body);
		}
		return $this->return_msg;
	}

	/**
     * @param string $email
     * @param string $reason
     * @param string $value
     * @param string $course_id
     *
     * @return array
     */
	public function scores($email, $reason, $value, $course_id) {
		$this->form_params = [
			'user_email'=>$email,
			'reason'=>$reason,
			'value'=>$value,
			'course_id'=>$course_id,
			'api_key'=>$this->api_key,
		];
		
		$request = new Request('POST', $this->getURL('POST', 'scores'), $this->getOptions($this->form_params), null);

		$response = $this->http->send($request, $this->getOptions($this->form_params), null);

		$code = $response->getStatusCode();
		$body = (string)$response->getBody();

		$this->return_msg['code'] = $code;
		
		if ($code == 201) {
			$this->return_msg['message'] = 'success';
			$this->return_msg['content'] = json_decode($body);
			$this->return_msg['status'] = true;
		} else {
			$this->return_msg['message'] = json_decode($body);
		}
		return $this->return_msg;
	}

	/**
     * @param string $email
     * @param string $reason
     * @param string $course_id
     *
     * @return array
     */
	public function delete_scores($email, $reason, $course_id) {
		$this->form_params = [
			'user_email'=>$email,
			'reason'=>$reason,
			'course_id'=>$course_id,
			'api_key'=>$this->api_key,
		];
		
		$request = new Request('POST', $this->getURL('POST', 'delete_scores'), $this->getOptions($this->form_params), null);

		$response = $this->http->send($request, $this->getOptions($this->form_params), null);

		$code = $response->getStatusCode();
		$body = (string)$response->getBody();

		$this->return_msg['code'] = $code;
		
		if ($code == 201) {
			$this->return_msg['message'] = 'success';
			$this->return_msg['content'] = json_decode($body);
			$this->return_msg['status'] = true;
		} else {
			$this->return_msg['message'] = json_decode($body);
		}
		return $this->return_msg;
	}

	/**
     * @param string $email
     * @param string $course_id
     *
     * @return array
     */
	public function delete_lesson_statuses($email, $course_id) {
		$this->form_params = [
			'user_email'=>$email,
			'course_id'=>$course_id,
			'api_key'=>$this->api_key,
		];
		
		$request = new Request('DELETE', $this->getURL('DELETE', 'delete_lesson_statuses'), $this->getOptions($this->form_params), null);
		$response = $this->http->send($request, $this->getOptions($this->form_params), null);

		$code = $response->getStatusCode();
		$body = (string)$response->getBody();

		$this->return_msg['code'] = $code;
		
		if ($code == 201) {
			$this->return_msg['message'] = 'success';
			$this->return_msg['content'] = json_decode($body);
			$this->return_msg['status'] = true;
		} else {
			$this->return_msg['message'] = json_decode($body);
		}
		return $this->return_msg;
	}

	/**
     * @return array
     */
	public function membership_levels() 
	{
		$request = new Request('GET', $this->getURL('GET', 'membership_levels'), $this->getOptions($this->form_params), null);

		$response = $this->http->send($request, $this->getOptions($this->form_params), null);

		$code = $response->getStatusCode();
		$body = (string)$response->getBody();

		$this->return_msg['code'] = $code;
		
		if ($code == 200) {
			$this->return_msg['message'] = 'success';
			$this->return_msg['content'] = json_decode($body);
			$this->return_msg['status'] = true;
		} else {
			$this->return_msg['message'] = json_decode($body);
		}
		return $this->return_msg;
	}

	/**
     * @return array
     */
	public function classrooms() 
	{
		$request = new Request('GET', $this->getURL('GET', 'classrooms'), $this->getOptions($this->form_params), null);

		$response = $this->http->send($request, $this->getOptions($this->form_params), null);

		$code = $response->getStatusCode();
		$body = (string)$response->getBody();

		$this->return_msg['code'] = $code;
		
		if ($code == 200) {
			$this->return_msg['message'] = 'success';
			$this->return_msg['content'] = json_decode($body);
			$this->return_msg['status'] = true;
		} else {
			$this->return_msg['message'] = json_decode($body);
		}
		return $this->return_msg;
	}

	/**
     * @return array
     */
	public function rankings() {
		$request = new Request('GET', $this->getURL('GET', 'rankings'), $this->getOptions($this->form_params), null);

		$response = $this->http->send($request, $this->getOptions($this->form_params), null);

		$code = $response->getStatusCode();
		$body = (string)$response->getBody();

		$this->return_msg['code'] = $code;
		
		if ($code == 200) {
			$this->return_msg['message'] = 'success';
			$this->return_msg['content'] = json_decode($body);
			$this->return_msg['status'] = true;
		} else {
			$this->return_msg['message'] = json_decode($body);
		}
		return $this->return_msg;
	}

	/**
     * @param int $id
     *
     * @return array
     */
	public function user_ranking($id) {
		$request = new Request('GET', $this->getURL('GET', 'user_ranking', $id), $this->getOptions($this->form_params), null);

		$response = $this->http->send($request, $this->getOptions($this->form_params), null);

		$code = $response->getStatusCode();
		$body = (string)$response->getBody();

		$this->return_msg['code'] = $code;
		
		if ($code == 200) {
			$this->return_msg['message'] = 'success';
			$this->return_msg['content'] = json_decode($body);
			$this->return_msg['status'] = true;
		} else {
			$this->return_msg['message'] = json_decode($body);
		}
		return $this->return_msg;
	}

    /**
     * @param int $id
     * Listar Feed de Atividades
     * @return array
     */
    public function user_activities($id)
    {
        $request = new Request('GET', $this->getURL('GET', 'user_activities', $id), $this->getOptions($this->form_params), null);
        $response = $this->http->send($request, $this->getOptions($this->form_params), null);

        $code = $response->getStatusCode();
        $body = (string)$response->getBody();

        $this->return_msg['code'] = $code;

        if ($code == 200) {
            $this->return_msg['message'] = 'success';
            $this->return_msg['content'] = json_decode($body);
            $this->return_msg['status'] = true;
        } else {
            $this->return_msg['message'] = json_decode($body);
        }
        return $this->return_msg;
    }

    /**
     * @param int $id
     * Lista todos os cursos cadastrados.
     * @return array
     */
    public function courses()
    {
        $request = new Request('GET', $this->getURL('GET', 'courses'), $this->getOptions($this->form_params), null);
        $response = $this->http->send($request, $this->getOptions($this->form_params), null);

        $code = $response->getStatusCode();
        $body = (string)$response->getBody();

        $this->return_msg['code'] = $code;


        if ($code == 200) {
            $this->return_msg['message'] = 'success';
            $this->return_msg['content'] = json_decode($body);
            $this->return_msg['status'] = true;
        } else {
            $this->return_msg['message'] = json_decode($body);
        }
        return $this->return_msg;
    }

    /**
     * @param int $id
     * Retorna dados básicos do curso, lista de módulos e respectivas aulas.
     * @return array
     */
    public function course($id)
    {
        $request = new Request('GET', $this->getURL('GET', 'course', $id), $this->getOptions($this->form_params), null);
        $response = $this->http->send($request, $this->getOptions($this->form_params), null);

        $code = $response->getStatusCode();
        $body = (string)$response->getBody();

        $this->return_msg['code'] = $code;


        if ($code == 200) {
            $this->return_msg['message'] = 'success';
            $this->return_msg['content'] = json_decode($body);
            $this->return_msg['status'] = true;
        } else {
            $this->return_msg['message'] = json_decode($body);
        }
        return $this->return_msg;
    }







}