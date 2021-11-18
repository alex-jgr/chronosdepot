<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Description of Contact
 *
 * @author admin
 */
class Model_Project_Contact extends ORM
{
    protected $_table_name      = 'project_contacts';
    protected $_table_columns   = array(
        'id'            => array('type' => 'int'),
        'user_id'       => array('type' => 'int'),
        'project_id'    => array('type' => 'int'),
        'email'         => array('type' => 'string'),
        'type'          => array('type' => 'string'),
        'hash'          => array('type' => 'string'),
        'active'        => array('type' => 'int')
    );
    
    protected $_belongs_to = array(
        'user'      => array('model' => 'User',     'foreign_key' => 'user_id'),
        'project'   => array('model' => 'Project',  'foreign_key' => 'project_id')
    );
    
    public function send_email()
    {
        $headers =  'From: admin@chronosdepot.com' . "\r\n" .
                    'Reply-To: admin@chronosdepot.com' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();
        $link= URL::site('user/acceptinvite',true) . '/' . $this->hash;
        $message = $this->project->owner->firstname . " " . $this->project->owner->firstname . ' has added you as a project contact for the project ' . $this->project->name
                . '. Please follow the link provided in this email in order to accept his request.'
                . ' By following the link you will also have create a Chronos Depot account: '
                . '<a href="' . $link . '">' . $link . '</a>';
        mail($this->email, 'Chronos Depot invitation for project: ' . $this->project->name, $message, $headers);
    }
    
    public static function invite($email, $project_id, $type)
    {
        $contact = new Model_Project_Contact();
        $contact->email         = $email;
        $contact->project_id    = $project_id;
        $contact->type          = $type;
        $contact->hash          = md5(time() . $email . rand(1, 100)) . rand(1, 1000);
        $contact->save();
        $contact->send_email();
        return $contact;
    }
    
    public static function set_spectator($user_id, $project_id)
    {
        $contact = new Model_Project_Contact();
        $contact->user_id       = $user_id;
        $contact->project_id    = $project_id;
        $contact->type          = 'spectator';
        $contact->save();
        $contact->send_email();
        return $contact;
    }


    public function activate($user_id)
    {
        $this->user_id  = $user_id;
        $this->type     = 'spectator';
        $this->hash     = NULL;
        $this->active   = 1;
        $this->save();
    }
    
    public static function pending_invites($project_id)
    {
        return DB::select(DB::expr('project_contacts.id as contact_id, user_id, firstname, lastname, public_path AS image, project_contacts.email as contact_email, users.email as user_email'))
                ->from('project_contacts')
                ->join('users', 'LEFT')->on('users.id', '=', 'project_contacts.user_id')
                ->join('image_versions', 'left')
                    ->on('users.image_id', '=', 'image_versions.image_id')
                    ->on('image_versions.name', '=', DB::expr('\'thumb\''))
                ->where('project_contacts.active', '=', '0')
                ->where('project_contacts.project_id', '=', $project_id)
                ->execute()->as_array();
    }
    
    public static function active_contacts($project_id)
    {
        return DB::select(DB::expr('project_contacts.id as contact_id, user_id, firstname, lastname, public_path AS image, project_contacts.email as contact_email, users.email as user_email'))
                ->from('project_contacts')
                ->join('users')->on('users.id', '=', 'project_contacts.user_id')
                ->join('image_versions', 'left')
                    ->on('users.image_id', '=', 'image_versions.image_id')
                    ->on('image_versions.name', '=', DB::expr('\'thumb\''))
                ->where('project_contacts.active', '=', '1')
                ->where('project_contacts.project_id', '=', $project_id)
                ->execute()->as_array();
    }
}
