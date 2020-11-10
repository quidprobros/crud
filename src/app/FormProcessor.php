<?PHP
namespace App;


class FormProcessor {
    public $postData;

    public function __construct($postData) {
        $this->postData = $postData;
    }
    
    public function fork() {
        return 555;
    }

}
