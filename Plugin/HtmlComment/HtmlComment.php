<?php
namespace Plugin\HtmlComment;

class HtmlComment extends \Plugin\AbstractPlugin {
    private $comments = [];
    
    public function calculate() {
        foreach($this->parameters['fields'] as $field) {
            $matches = false;
            preg_match_all('/<!--.*-->/', $this->data[$field], $matches);
            if(is_array($matches) && count($matches)>0) {
                foreach($matches as $match) {
                    if(isset($match[0]) && trim($match[0]) != '') {
                        $this->comments[$field][] = $match[0];
                    }
                }
            }
        }
        if(count($this->comments)>0) {
            $this->setSuccess(true);
        }
    }
    
    public function getResult() {
        return [
            'comments' => $this->comments,
        ];
    }
    
    public function getOutput() {
        $source = '';
        if(count($this->comments)>0) {
            foreach($this->comments as $fieldname => $comments) {
                $source.= '<h4>'.$fieldname.'</h4>'.PHP_EOL;
                foreach($comments as $comment) {
                    $source.= '<p>'.htmlentities($comment).'</p>'.PHP_EOL;
                }
            }
        }
        return $source;
    }
}
