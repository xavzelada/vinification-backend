<?php

namespace App\DataFixtures;

use App\Entity\AnalysisType;
use App\Entity\Bodega;
use App\Entity\Product;
use App\Entity\RecommendationRule;
use App\Entity\Stage;
use App\Entity\User;
use App\Entity\AlertRule;
use App\Enum\AlertSeverity;
use App\Enum\RuleOperator;
use App\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $bodega = new Bodega();
        $bodega->setCodigo('BODEGA-01')
            ->setNombre('Bodega Demo')
            ->setPais('ES');
        $manager->persist($bodega);

        $admin = new User();
        $admin->setEmail('admin@bodega.test')
            ->setNombre('Admin')
            ->setRoles([UserRole::ADMIN])
            ->setBodega($bodega)
            ->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        $stages = [
            'Recepcion',
            'Desfangado',
            'Fermentacion alcoholica',
            'Maceracion',
            'Prensado',
            'Fermentacion malolactica',
            'Crianza',
            'Estabilizacion',
            'Filtracion',
            'Embotellado'
        ];
        $order = 1;
        $stageEntities = [];
        foreach ($stages as $name) {
            $stage = new Stage();
            $stage->setNombre($name)->setOrden($order++)->setBodega($bodega);
            $manager->persist($stage);
            $stageEntities[] = $stage;
        }

        $analysisTypes = [
            ['densidad', 'Densidad', 'g/cm3'],
            ['temperatura', 'Temperatura', 'C'],
            ['ph', 'pH', 'pH'],
            ['acidez_total', 'Acidez total', 'g/L'],
            ['so2_libre', 'SO2 libre', 'mg/L'],
            ['so2_total', 'SO2 total', 'mg/L'],
            ['azucares', 'Azucares', 'g/L'],
            ['yan', 'YAN', 'mg/L'],
            ['va', 'Acidez volatil', 'g/L']
        ];
        foreach ($analysisTypes as [$codigo, $nombre, $unidad]) {
            $type = new AnalysisType();
            $type->setCodigo($codigo)->setNombre($nombre)->setUnidad($unidad);
            $manager->persist($type);
        }

        $products = [
            ['Sulfito potasico', 'Conservante', 'mg/L'],
            ['Nutriente de levaduras', 'Nutriente', 'g/hL'],
            ['Enzima pectolitica', 'Enzima', 'g/hL'],
            ['Bentonita', 'Clarificante', 'g/hL'],
            ['Taninos enologicos', 'Tanino', 'g/hL'],
            ['Acido tartarico', 'Acidificacion', 'g/L'],
            ['Carbonato de calcio', 'Desacidificacion', 'g/L'],
            ['Levadura seleccionada', 'Levadura', 'g/hL'],
            ['Nutriente organico', 'Nutriente', 'g/hL'],
            ['Clarificante vegetal', 'Clarificante', 'g/hL']
        ];
        foreach ($products as [$name, $cat, $unit]) {
            $product = new Product();
            $product->setBodega($bodega)
                ->setNombre($name)
                ->setCategoria($cat)
                ->setUnidad($unit);
            $manager->persist($product);
        }

        $fermentStage = $stageEntities[2] ?? null;
        if ($fermentStage) {
            $rule = new AlertRule();
            $rule->setBodega($bodega)
                ->setEtapa($fermentStage)
                ->setNombre('Densidad alta en fermentacion')
                ->setParametro('densidad')
                ->setOperador(RuleOperator::GT)
                ->setValor('1.0200')
                ->setSeveridad(AlertSeverity::WARN)
                ->setActiva(true);
            $manager->persist($rule);

            $recRule = new RecommendationRule();
            $recRule->setBodega($bodega)
                ->setEtapa($fermentStage)
                ->setNombre('Revisar fermentacion')
                ->setCondiciones([
                    ['field' => 'densidad', 'operator' => '>', 'value' => 1.020]
                ])
                ->setAccionSugerida('Revisar temperatura y densidad; considerar aireacion suave si procede.')
                ->setExplicacion('Regla heuristica basada en densidad. Sugerencia no vinculante.')
                ->setActiva(true);
            $manager->persist($recRule);
        }

        $manager->flush();
    }
}
