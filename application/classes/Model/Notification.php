<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Notification extends ORM
{
    protected $_table_name = 'notifications';
    
    protected $_table_columns = array(
        'id'          => array('type' => 'int'),
        'user_id'     => array('type' => 'int'),
        'type'        => array('type' => 'string'),
        'info'        => array('type' => 'string',  'null' => TRUE),
        'status'      => array('type' => 'string'),
        'created'     => array('type' => 'int'),
    );
    
    public $_belongs_to = array(
        'user' => array('model' => 'User', 'foreign_key' => 'user_id')
    );
    
    public static function get_user_notifications($user_id, $limit, $offset, $start_date, $end_date)
    {
        $sql = DB::select(
                    DB::expr('*, DATE_FORMAT(FROM_UNIXTIME(created), \'%H:%i, %d %M %Y\') AS date')
                )
                ->from('notifications')
                ->where('user_id', '=', $user_id)
                ->order_by('created', 'DESC')
                ->limit($limit)
                ->offset($offset);
        
        if ($start_date) {
            $sql->where('created', '>=', $start_date);
        }
        
        if ($end_date) {
            $sql->where('created', '<=', $end_date);
        }
        
        return $sql->execute()->as_array();
    }


    public static function team_join_request(Model_Team $team, Model_User $user)
    {
        $notification = new Model_Notification();
        $notification->user_id = $team->user_id;
        $notification->type    = 'team_join_request';
        $notification->info    = '<a href="/user/profile/' . $user->id . '">' 
                                    . $user->username 
                                . '</a> is asking for permisson to join: <a href="/team/manage/' . $team->id . '">' 
                                    . $team->name 
                                . '</a>.';
        $notification->created = time();
        $notification->save();
    }
    
    public static function team_invite_request(Model_Team $team, Model_User $user)
    {
        $notification = new Model_Notification();
        $notification->user_id = $user->id;
        $notification->type    = 'team_invite_request';
        $notification->info    = '<a href="/user/profile/' . $team->user_id . '">' 
                                    . $team->user->username 
                                . '</a> has invited you to join his team: <a href="/team/profile/' . $team->id . '">' 
                                    . $team->name 
                                . '</a>.';
        $notification->created = time();
        $notification->save();
    }
    
    public static function team_cancel_request(Model_Team_Request $request)
    {
        $notification = new Model_Notification();        
        if ($request->type == 'invite') {
            $notification->user_id = $request->user_id;
            $notification->type = 'team_cancel_invite';
            $notification->info    = 'Invitation to team team: <a href="/team/profile/' . $request->team->id . '"> ' 
                                    . $request->team->name 
                                . '</a> has been cancelled.';
        } else {
            $notification->user_id = $request->user_id;
            $notification->type = 'team_cancel_join';
            $notification->info    = 'Team join request for team: <a href="/team/profile/' . $request->team->id . '"> ' 
                                    . $request->team->name
                                . '</a> has been cancelled.';
        }
        $notification->created = time();
        $notification->save();
    }
    
    public static function accept_team_join(Model_Team $team, Model_User $user)
    {
        $notification = new Model_Notification();
        $notification->user_id = $user->id;
        $notification->type    = 'accept_team_join';
        $notification->info    = '<a href="/user/profile/' . $team->user_id . '">' 
                                    . $team->user->username 
                                . '</a> has approved your request to join: <a href="/team/profile/' . $team->id . '">' 
                                    . $team->name 
                                . '</a>.';
        $notification->created = time();
        $notification->save();
    }
    
    public static function accept_team_invite(Model_Team $team, Model_User $user)
    {
        $notification = new Model_Notification();
        $notification->user_id = $team->user->id;
        $notification->type    = 'accept_team_invite';
        $notification->info    = '<a href="/user/profile/' . $user->id . '">' 
                                    . $user->username 
                                . '</a> has accepted your invitation to: <a href="/team/profile/' . $team->id . '">' 
                                    . $team->name 
                                . '</a>.';
        $notification->created = time();
        $notification->save();
    }

    public static function team_accept(Model_Team_Request $request)
    {
        if ($request->type == 'join') {
            self::accept_team_join($request->team, $request->user);
        } elseif($request->type == 'invite') {
            self::accept_team_invite($request->team, $request->user);
        }
    }
    
    public static function team_leave(Model_Team $team, Model_User $user) 
    {
        $notification = new Model_Notification();
        $notification->user_id = $team->user->id;
        $notification->type    = 'team_leave';
        $notification->info    = '<a href="/user/profile/' . $user->id . '">' 
                                    . $user->username 
                                . '</a> has left the team: <a href="/team/profile/' . $team->id . '">' 
                                    . $team->name 
                                . '</a>.';
        $notification->created = time();
        $notification->save();
    }
    
    public static function team_exclude(Model_Team $team, Model_User $user) {
        $notification = new Model_Notification();
        $notification->user_id = $user->id;
        $notification->type    = 'team_exclude';
        $notification->info    = '<a href="/user/profile/' . $team->user->id . '">' 
                                    . $team->user->username 
                                . '</a> has excluded you from the team: <a href="/team/profile/' . $team->id . '">' 
                                    . $team->name 
                                . '</a>.';
        $notification->created = time();
        $notification->save();
    }
}