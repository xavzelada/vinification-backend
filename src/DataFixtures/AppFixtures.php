<?php

namespace App\DataFixtures;

use App\Entity\AnalysisType;
use App\Entity\Alert;
use App\Entity\Bodega;
use App\Entity\Batch;
use App\Entity\Measurement;
use App\Entity\Product;
use App\Entity\Recommendation;
use App\Entity\RecommendationRule;
use App\Entity\Stage;
use App\Entity\User;
use App\Entity\AlertRule;
use App\Enum\AlertSeverity;
use App\Enum\AlertStatus;
use App\Enum\BatchStatus;
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

        $ruleByStage = [];
        $recRuleByStage = [];
        foreach ($stageEntities as $stage) {
            $rule = new AlertRule();
            $rule->setBodega($bodega)
                ->setEtapa($stage)
                ->setNombre('Densidad alta - ' . $stage->getNombre())
                ->setParametro('densidad')
                ->setOperador(RuleOperator::GT)
                ->setValor('1.0200')
                ->setSeveridad(AlertSeverity::WARN)
                ->setActiva(true);
            $manager->persist($rule);
            $ruleByStage[$stage->getId()] = $rule;

            $recRule = new RecommendationRule();
            $recRule->setBodega($bodega)
                ->setEtapa($stage)
                ->setNombre('Revisar etapa - ' . $stage->getNombre())
                ->setCondiciones([
                    ['field' => 'densidad', 'operator' => '>', 'value' => 1.020]
                ])
                ->setAccionSugerida('Revisar temperatura y densidad; considerar ajustes si procede.')
                ->setExplicacion('Regla heuristica basada en densidad. Sugerencia no vinculante.')
                ->setActiva(true);
            $manager->persist($recRule);
            $recRuleByStage[$stage->getId()] = $recRule;
        }

        // Batches de prueba en distintas etapas con alertas y recomendaciones
        $now = new \DateTimeImmutable();
        $testBatches = [
            ['L-REC-01', $stageEntities[0] ?? null, 'Cabernet', 2024, '1.0900', '18.5'], // recepcion
            ['L-DES-02', $stageEntities[1] ?? null, 'Syrah', 2024, '1.0700', '16.0'],   // desfangado
            ['L-FER-03', $stageEntities[2] ?? null, 'Malbec', 2024, '1.0300', '24.5'],  // fermentacion
            ['L-MAC-04', $stageEntities[3] ?? null, 'Merlot', 2024, '1.0150', '26.0'],  // maceracion
            ['L-MLF-05', $stageEntities[5] ?? null, 'Tempranillo', 2024, '1.0000', '20.0'] // malolactica
        ];

        foreach ($testBatches as [$code, $stage, $variety, $year, $densidad, $temp]) {
            if (!$stage) {
                continue;
            }
            $batch = new Batch();
            $batch->setCodigo($code)
                ->setVolumenLitros('1200')
                ->setVariedad($variety)
                ->setCosechaYear($year)
                ->setBodega($bodega)
                ->setEtapa($stage)
                ->setEstado(BatchStatus::ACTIVE)
                ->setFechaInicio($now);
            $manager->persist($batch);

            $measurement = new Measurement();
            $measurement->setLote($batch)
                ->setUsuario($admin)
                ->setFechaHora($now)
                ->setDensidad($densidad)
                ->setTemperaturaC($temp);
            $manager->persist($measurement);

            $rule = $ruleByStage[$stage->getId()] ?? null;
            if ($rule) {
                $alert = new Alert();
                $alert->setLote($batch)
                    ->setRegla($rule)
                    ->setSeveridad(AlertSeverity::WARN)
                    ->setEstado(AlertStatus::OPEN)
                    ->setMensaje('Alerta de prueba para ' . $code)
                    ->setDetectedAt($now);
                $manager->persist($alert);
            }

            $rec = new Recommendation();
            $rec->setLote($batch)
                ->setEtapa($stage)
                ->setAccionSugerida('Recomendacion de prueba para ' . $code)
                ->setExplicacion('Regla de ejemplo para QA.')
                ->setConfidence('0.65');
            $manager->persist($rec);
        }

        $manager->flush();
    }
}
