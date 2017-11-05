<?php
namespace Plugin\OwnerLogs;

use Helper\ApiHelper;
use Helper\CoordsHelper;
use Helper\Utf8Helper;
use Model\logModel;

class OwnerLogs extends \Plugin\AbstractPlugin {
    private $OwnerLogs = [];

    public function calculate() {
        $data = ApiHelper::getLogData();

        if($data==null) {
            return;
        }
        
        foreach($data['Logs'] as $log) {
            $isOwnerLog = $log['Finder']['UserName'] == $this->data['Owner']['UserName'];
            
            if ($isOwnerLog || $log['LogType']['WptLogTypeName'] == 'Publish Listing') {
                $logModel = new LogModel();
                $logModel->visitDate = date_format(date_create($log['VisitDateIso']), "d M Y h:i");
                $logModel->logType = $log['LogType']['WptLogTypeName'];
                $logModel->text = $log['LogText'];
                $logModel->author = $log['Finder']['UserName'];

                $this->OwnerLogs[] = $logModel;
                if ($isOwnerLog)
                {
                    $this->setSuccess(true);
                }
            }
        }
    }

    public function getResult() {
        return $this->OwnerLogs;
    }
    
    public function getOutput() {
        $source = '';
        
        if(count($this->OwnerLogs) > 0) {
            foreach($this->OwnerLogs as $log) {
                $source.= '<div class="row">'.PHP_EOL;
                $source.= '  <div class="col-lg-6">'.PHP_EOL;
                $source.= '    <h4>'.$log->visitDate.' - '.$log->logType.' - '.$log->author.'</h4>'.PHP_EOL;
                $source.= '    <pre class="pre-scrollable">'.print_r($log->text, true).'</pre>'.PHP_EOL;
                $source.= '  </div>'.PHP_EOL;
                $source.= '</div>'.PHP_EOL;
            }
        }

        return $source;
    }
}