<?php
namespace Plugin\HtmlComment;

class HtmlComment extends \Plugin\AbstractPlugin {
    private $comments = [];
    
    public function calculate() {
        foreach($this->parameters['fields'] as $field) {
            $matches = false;
            preg_match_all('/<!--(.*?)-->/s', $this->data[$field], $matches);
            if(is_array($matches[1]) && count($matches[1])>0) {
                foreach($matches[1] as $match) {
                    if(trim($match)!='') {
                        $this->comments[$field][] = $match;
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
