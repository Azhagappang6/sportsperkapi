<?php

namespace App\Services;
// 
// use App\Models\HKUser as User;
use App\Modals\HKModal as Model;
use App\Modals\HKUser as User;
use App\Modals\HKCoach as Coach;
use App\Modals\HKPlayer as Player;
use App\Modals\HKTeam as Team;
use App\Modals\HKGame as Game;
use App\Modals\HKRegion as Region;
use App\Modals\HKFavourites as Favourites;
use App\Modals\HKTeamAttendance as TeamAttendance;
use App\Modals\HKAttendance as Attendance;
use App\Modals\HKAttendance2 as Attendance2;
use App\Modals\HKAttend as Attend;

// use App\Models\HKVendor as Vendor;
// use App\Models\HKGroup as Group;
// use App\Models\HKCategory as Category;
// use App\Models\HKProduct as Product;
// use App\Models\HKCustomer as Customer;
use App\Modals\Helpers\SPResponse as Response;
// 
use App\Controller\MainController;
use App\Services\HKContentService as CTS;
use App\Services\HKUtilityService as UTS;
use App\Services\HKDatabaseService as DBS;
// 
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class HKContentService
{
    public $request;
    public static $user_id;
    public static $data;

    function __construct(MainController $controller, $request)
    {
        DBS::$db_connection = $controller->GetConnection();
        if ($request instanceof HttpRequest) {
            $this->request = $request;
            CTS::$user_id = $request->get('user_id');
        }
    }

    #region ***** Request Helpers *****
    public function request_object()
    {
        if ($this->request instanceof HttpRequest) {
            return $this->request->get('object');
        }
    }

    public function request_filter()
    {
        if ($this->request instanceof HttpRequest) {
            return $this->request->get('filter');
        }
    }

    public function extra_params()
    {
        return UTS::array_from($this->request_filter(), "extra_params", []);
    }
    #endregion

    #region
    public function execute($controller, $action, $type_or_id = NULL)
    {
        if ($action == 'create') return $this->execute_create($controller);
        else if ($action == 'read') return $this->execute_read($controller, $type_or_id);
        else if ($action == 'update') return $this->execute_update($controller, $type_or_id);
        else if ($action == 'delete') return $this->execute_delete($controller, $type_or_id);
        else if ($action == 'list') return $this->execute_list($controller, $type_or_id);
        else if ($action == 'list-count') return $this->execute_listcount($controller, $type_or_id);
        //new one 
        else if ($action == 'authenticate') return $this->execute_authenticate($controller);
    }
    #endregion

    #region
    private function execute_authenticate($controller)
    {
         $model = CTS::model($controller, $this->request_object());
         if ($model instanceof Model) {
            $result = $model->db_authenticate($this->request_filter());
            if ($result && $result instanceof Model) {
                return new Response(true, NULL, [$controller => [$result]]);
            }
        }
        return new Response(false);
        // $model = CTS::model($controller, $this->request_object());
        // $result = $model->db_authenticate($this->request_filter());
        // if($result!=null && $result->id){
        //     return $result;
        // } 
        
        // return new Response(false,null,"Invalid Credentials");
    }
    private function execute_create($controller)
    {
        $model = CTS::model($controller, $this->request_object());
        if($controller == 'attendance'){
            if ($model->db_create()) {
                return new Response(true);
            }
        }else
        {
            if ($model->db_create()) {
                return $this->execute_read($controller, $model->id);
            }
        }
        
        return new Response(false);
    }

    private function execute_read($controller, $id)
    {
        $model = CTS::model($controller, [Model::DB_ID => $id]);
        if ($model instanceof Model) {
            $result = $model->db_read($this->request_filter());
            if ($result && $result instanceof Model) {
                return new Response(true, NULL, [$controller => [$result]]);
            }
        }
        return new Response(false);
    }

    private function execute_update($controller, $id)
    {
        $model = CTS::model($controller, $this->request_object());
        if ($model->db_update()) {
            return $this->execute_read($controller, $model->id);
        }
        return new Response(false);
    }

    private function execute_delete($controller, $id)
    {
        $model = CTS::model($controller, [Model::DB_ID => $id]);
        if ($model->db_delete()) {
            return new Response(true);
        }
        return new Response(false);
    }

    private function execute_list($controller, $type)
    {
        if ($controller == "users") $objects = User::db_list($type, $this->request_filter());
        // if ($controller == "categories") $objects = Group::db_list($type, $this->request_filter());
        else if ($controller == "coaches") $objects = Coach::db_list($type, $this->request_filter());
        else if ($controller == "players") $objects = Player::db_list($type, $this->request_filter());
        else if ($controller == "teams") $objects = Team::db_list($type, $this->request_filter());
        else if ($controller == "year") $objects = Game::db_list($type, $this->request_filter());
        else if ($controller == "regions") $objects = Region::db_list($type, $this->request_filter());
		 else if ($controller == "attendance") $objects = Attend::db_list($type, $this->request_filter());
		 else if ($controller == "attendance2") $objects = Attendance2::db_list($type, $this->request_filter());
		 else if ($controller == "favourites") $objects = Favourites::db_list($type, $this->request_filter());
        // else if ($controller == "attendances") $objects = Attendance::db_list($type, $this->request_filter());
        // else if ($controller == "customers") $objects = Customer::db_list($type, $this->request_filter());
        // else if ($controller == "categories") $objects = Customer::db_list($type, $this->request_filter());
        return ($objects) ? new Response(true, NULL, array($controller => $objects)) : new Response(false);
    }

    private function execute_listcount($controller, $type)
    {
    }
    #endregion

    #region
    private static function model($controller, $object)
    {
        if ($controller == 'users') return new User($object);
         if ($controller == 'coaches') return new Coach($object);
         else if ($controller == 'players') return new Player($object);
         else if ($controller == 'teams') return new Team($object);
         else if ($controller == 'games') return new Game($object);
         else if ($controller == 'regions') return new Regions($object);
         // else if ($controller == 'attendances') return new TeamAttendance($object);
         else if ($controller == 'attendance') return new Attendance($object);
         else if ($controller == 'attendance2') return new Attendance2($object);
		 else if ($controller == 'favourites') return new Favourites($object);
        // else if ($controller == 'products') return new Product($object);
        // else if ($controller == 'customers') return new Customer($object);
    }
    #endregion
}
