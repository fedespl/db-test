<?

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class TestBase extends TestCase
{   
    protected $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();
    }
}