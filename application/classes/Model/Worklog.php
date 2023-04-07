<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Description of Task
 *
 * @author Alexandru
 */
class Model_Worklog extends ORM
{
    protected $_table_name      = 'worklogs';

    protected $_table_columns   = array(
        'id'                => array('type' => 'int'),
        'task_id'           => array('type' => 'int'),
        'user_id'           => array('type' => 'int'),
        'duration'          => array('type' => 'int'),
        'active'            => array('type' => 'int'),
        'start_time'        => array('type' => 'int'),
        'stop_time'         => array('type' => 'int',   'null' => TRUE),
        'note'              => array('type' => 'string','null' => TRUE),
        'budget_spent'      => array('type' => 'float'),
        'modified'          => array('type' => 'int' ,  'null' => TRUE),
        'original_duration' => array('type' => 'int')
    );
    
    protected $_belongs_to = array(
        'user'      => array('model' => 'User'),
        'task'      => array('model' => 'Task'),
    );
    
    public static function common_query()
    {
        return DB::select(DB::expr('worklogs.*, projects.name AS project_name, tasks.project_id, tasks.parent_id, tasks.level, tasks.goal AS task_goal, tasks.task_type_id, task_types.name as task_type, users.username'),
                    DB::expr('SEC_TO_TIME(duration) AS human_duration'),
                    DB::expr('DATE_FORMAT(FROM_UNIXTIME(start_time), \'%H:%i, %d %M %Y\') AS human_start'),
                    DB::expr('DATE_FORMAT(FROM_UNIXTIME(stop_time), \'%H:%i, %d %M %Y\') AS human_stop')
                )
                ->from('worklogs')
                ->join('tasks')->on('tasks.id', '=', 'worklogs.task_id')
                ->join('projects')->on('projects.id', '=', 'tasks.project_id')
                ->join('users')->on('users.id', '=', 'worklogs.user_id')
                ->join('task_types', 'LEFT')->on('tasks.task_type_id', '=', 'task_types.id');
    }

    public static function get_leader_worklogs($user_id, $start = NULL, $end = NULL)
    {
        $query = DB::select(DB::expr('worklogs.*, projects.name AS project_name'))
                ->from('worklogs')
                ->join('projects')->on('worklogs.project_id','=','projects.id')
                ->join('users')->on('project.user_id', '=', 'users.id')
                ->where('users.id','=', $user_id);
        if ($start) {
            $query->where('worklogs.start_time', '>=', $start);
        }
        if ($end) {
            $query->where('worklogs.end', '<=', $end);
        }
        
        return $query->execute()->as_array();
    }
    
    public static function get_personal_worklogs($user_id, $start = NULL, $end = NULL)
    {
        $query = self::common_query()
                ->where('worklogs.user_id','=', $user_id);
        if ($start) {
            $query->where('worklogs.start_time', '>=', $start);
        }
        if ($end) {
            $query->where('worklogs.end', '<=', $end);
        }
        
        return $query->execute()->as_array();
    }

    public static function get_project_worklogs($project_id, $user_id, $start = NULL, $end = NULL)
    {
        $query = self::common_query()
                ->where('tasks.project_id', '=', $project_id);
        if ($user_id) {
            $query->where('worklogs.user_id', '=', $user_id);
        }
        
        if ($start) {
            $query->where('worklogs.start_time', '>=', $start);
        }
        
        if ($end) {
            $query->where('worklogs.stop_time', '<=', $end);
        }
        
        return $query->execute()->as_array();
    }
    
    public static function get_project_excel(Model_Project $project, $start = NULL, $end = NULL)
    {
        $query = self::common_query()
                ->select(DB::expr('parents.goal AS parent_goal'))
                ->join(DB::expr('tasks AS parents'), 'LEFT')->on('parents.id', '=', 'tasks.parent_id')
                ->where('tasks.project_id', '=', $project->id);
        
        $spreadsheetSubject = '';
        
        if ($start) {
            $query->where('worklogs.start_time', '>=', $start);
            $spreadsheetSubject .=  date('d / m - Y', $start);
        }
        
        if ($end) {
            $query->where('worklogs.stop_time', '<=', $end);
            $spreadsheetSubject .=  ' - '. date('d / m - Y', $start);
        }
        $worklogs = $query->execute()->as_array();
        $sheetTitle = str_replace(array('*', ':', '/', '\\', '?', '[', '}'), ' ', $project->name);
        
        $spreadsheet = new PHPExcel();
        $spreadsheet->getProperties()->setCreator($project->owner->email)
                                     ->setLastModifiedBy('ChronosDepot.com')
                                     ->setTitle($sheetTitle)
                                     ->setSubject($spreadsheetSubject)
                                     ->setDescription('Worklogs for the project ' . $project->name)
                                     ->setKeywords('worklog project ' . $project->name)
                                     ->setCategory('Worklogs');
        
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setTitle($sheetTitle);
        $spreadsheet->getActiveSheet()
                ->setCellValue('A1', 'User')
                ->setCellValue('B1', 'Parent task')
                ->setCellValue('C1', 'Task')
                ->setCellValue('D1', 'Duration')
                ->setCellValue('E1', 'Start time')
                ->setCellValue('F1', 'Comment')
                ->setCellValue('G1', 'Duration in seconds');
       $spreadsheet->getActiveSheet()->getRowDimension()->setRowHeight(28);
       $spreadsheet->getActiveSheet()
                ->getStyle('A1:G1')
		->applyFromArray(array(
                    'font'  => array(
                        'bold'  => true
                    ),
                    'alignment' => array(
                       'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                    ),
                    'borders' => array(
                        'top' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                    ),
                    'fill' => array(
                        'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                        'rotation'   => 90,
                        'startcolor' => array(
                            'argb' => 'FFA0A0A0'
                        ),
                        'endcolor'   => array(
                            'argb' => 'FFFFFFFF'
                        )
                    )
		)
            );
       $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getAlignment()->setWrapText(true);
//        worklogs.*, 
//        projects.name AS project_name, 
//        tasks.project_id, 
//        tasks.parent_id, 
//        tasks.level, 
//        tasks.goal AS task_goal, 
//        tasks.task_type_id, 
//        task_types.name as task_type, 
//        users.username',
//        SEC_TO_TIME(duration) AS human_duration
//        DATE_FORMAT(FROM_UNIXTIME(start_time), \'%H:%i, %d %M %Y\') AS human_start'
//        DATE_FORMAT(FROM_UNIXTIME(stop_time), \'%H:%i, %d %M %Y\') AS human_stop
        $cellIndex = 2;
        foreach ($worklogs as $key => $worklog) {
            $cellIndex = $key + 2;
            $spreadsheet->getActiveSheet()
                    ->setCellValue('A' . $cellIndex, $worklog['username'])
                    ->setCellValue('B' . $cellIndex, $worklog['parent_goal'])
                    ->setCellValue('C' . $cellIndex, $worklog['task_goal'])
                    ->setCellValue('D' . $cellIndex, $worklog['human_duration'])
                    ->setCellValue('E' . $cellIndex, $worklog['human_start'])
                    ->setCellValue('F' . $cellIndex, $worklog['note'])
                    ->setCellValue('G' . $cellIndex, $worklog['duration']);
            $spreadsheet->getActiveSheet()->getStyle('A' . $cellIndex . ':G' . $cellIndex)->getAlignment()->setWrapText(true);
        }
        $lastIndex = $cellIndex + 3;
        
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(22);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(32);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(42);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(24);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(40);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        
        $spreadsheet->getActiveSheet()
                ->getStyle('A' . ($lastIndex - 1) . ':G' . $lastIndex)
                ->applyFromArray(array(
                    'font'  => array(
                        'bold'  => true
                    ),
                    'alignment' => array(
                       'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                    ),
                    'borders' => array(
                        'top' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                    ),
                    'fill' => array(
                        'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                        'rotation'   => 90,
                        'startcolor' => array(
                            'argb' => 'FFA0A0A0'
                        ),
                        'endcolor'   => array(
                            'argb' => 'FFFFFFFF'
                        )
                    )
		)
            );
        $spreadsheet->getActiveSheet()->setCellValue('G' . $lastIndex, '=SUM(G2:G'. $cellIndex .')')
                ->setCellValue('I1', '=INT(G' . $lastIndex . '/3600)')
                ->setCellValue('I2', '=INT(MOD(G' . $lastIndex . ',3600)/60)')
                ->setCellValue('I3', '=MOD(MOD(G' . $lastIndex . ',3600),60)')
                ->setCellValue('D' . ($lastIndex - 1), 'Hours')
                ->setCellValue('D' . $lastIndex, '=CONCATENATE(IF(I1 > 9,I1,CONCATENATE("0",I1)),":",IF(I2>9,I2,CONCATENATE("0",I2)),":",IF(I3>9,I3,CONCATENATE("0",I3)))')
                ->setCellValue('I4', $project->wage)
                ->setCellValue('B' . ($lastIndex - 1), 'Amount')
                ->setCellValue('B' . $lastIndex, '=CONCATENATE(G' . $lastIndex . ' * I4/3600, " - ' . $project->currency->code . '")')
                ->setCellValue('A' . ($lastIndex - 1), 'TOTALS:');
        $spreadsheet->getActiveSheet()
                ->getStyle('G'.$lastIndex)
                ->applyFromArray(array(
                    'font'  => array(
                        'color' => array('rgb' => 'FFA0A0A0')
                        )
                    )
                );
        $spreadsheet->getActiveSheet()->setSelectedCells('A1');
        return $spreadsheet;
    }
    
    public static function get_user_inactive_worklogs($user_id, $start = NULL, $end = NULL)
    {
        $query = self::common_query()
                ->where('worklogs.user_id', '=', $user_id);
        if ($start) {
            $query->where('worklogs.start_time', '>=', $start);
        }
        
        if ($end) {
            $query->where('worklogs.stop_time', '<=', $end);
        }
        
        return $query->where('worklogs.active', '=', 0)
                ->order_by('start_time')
                ->execute()->as_array();
    }
    
    public static function get_team_worklogs($team_id, $start = NULL, $end = NULL)
    {
        
    }
}
