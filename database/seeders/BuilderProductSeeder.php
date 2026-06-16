<?php

namespace Database\Seeders;

use App\Models\BuilderProduct;  // ← OVO NEDOSTAJE
use Illuminate\Database\Seeder;

class BuilderProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [

            // ── CPU-ovi ─────────────────────────────────────
            [
                'name'          => 'AMD Ryzen 7 9800X3D',
                'slug'          => 'amd-ryzen-7-9800x3d',
                'brand'         => 'AMD',
                'component_type'=> 'cpu',
                'price'         => 449.00,
                'in_stock'      => true,
                'perf_score'    => 820,
                'tdmark_base'   => 0,
                'fps_base_1080' => 0,
                'short_desc'    => '8C/16T · 5.7GHz boost · AM5 · 3D V-Cache',
                'specs'         => [
                    'socket'    => 'AM5',
                    'cores'     => 8,
                    'threads'   => 16,
                    'boost_ghz' => 5.7,
                    'tdp'       => 120,
                    'ram_type'  => 'DDR5',
                ],
            ],
            [
                'name'          => 'Intel Core i9-14900K',
                'slug'          => 'intel-i9-14900k',
                'brand'         => 'Intel',
                'component_type'=> 'cpu',
                'price'         => 549.00,
                'in_stock'      => true,
                'perf_score'    => 800,
                'tdmark_base'   => 0,
                'fps_base_1080' => 0,
                'short_desc'    => '24C/32T · 6.0GHz boost · LGA1700',
                'specs'         => [
                    'socket'    => 'LGA1700',
                    'cores'     => 24,
                    'boost_ghz' => 6.0,
                    'tdp'       => 253,
                    'ram_type'  => 'DDR5',
                ],
            ],
            [
                'name'          => 'AMD Ryzen 5 7600X',
                'slug'          => 'amd-ryzen-5-7600x',
                'brand'         => 'AMD',
                'component_type'=> 'cpu',
                'price'         => 229.00,
                'in_stock'      => true,
                'perf_score'    => 620,
                'tdmark_base'   => 0,
                'fps_base_1080' => 0,
                'short_desc'    => '6C/12T · 5.3GHz boost · AM5',
                'specs'         => [
                    'socket'    => 'AM5',
                    'cores'     => 6,
                    'tdp'       => 105,
                    'ram_type'  => 'DDR5',
                ],
            ],

            // ── GPU-ovi ─────────────────────────────────────
            [
                'name'          => 'NVIDIA RTX 4080 Super',
                'slug'          => 'rtx-4080-super',
                'brand'         => 'NVIDIA',
                'component_type'=> 'gpu',
                'price'         => 999.00,
                'in_stock'      => true,
                'perf_score'    => 950,
                'tdmark_base'   => 23800,
                'fps_base_1080' => 280,
                'short_desc'    => '16GB GDDR6X · DLSS 3.5 · Ada Lovelace',
                'specs'         => [
                    'vram_gb' => 16,
                    'tdp'     => 320,
                    'wattage' => 320,
                ],
            ],
            [
                'name'          => 'NVIDIA RTX 4070 Ti Super',
                'slug'          => 'rtx-4070-ti-super',
                'brand'         => 'NVIDIA',
                'component_type'=> 'gpu',
                'price'         => 799.00,
                'in_stock'      => true,
                'perf_score'    => 870,
                'tdmark_base'   => 18500,
                'fps_base_1080' => 230,
                'short_desc'    => '16GB GDDR6X · DLSS 3.5 · 285W TDP',
                'specs'         => ['vram_gb' => 16, 'tdp' => 285, 'wattage' => 285],
            ],
            [
                'name'          => 'AMD RX 7900 XTX',
                'slug'          => 'rx-7900-xtx',
                'brand'         => 'AMD',
                'component_type'=> 'gpu',
                'price'         => 879.00,
                'in_stock'      => true,
                'perf_score'    => 920,
                'tdmark_base'   => 22100,
                'fps_base_1080' => 265,
                'short_desc'    => '24GB GDDR6 · FSR 3 · RDNA 3',
                'specs'         => ['vram_gb' => 24, 'tdp' => 355, 'wattage' => 355],
            ],
            [
                'name'          => 'NVIDIA RTX 4070',
                'slug'          => 'rtx-4070',
                'brand'         => 'NVIDIA',
                'component_type'=> 'gpu',
                'price'         => 549.00,
                'in_stock'      => true,
                'perf_score'    => 780,
                'tdmark_base'   => 14500,
                'fps_base_1080' => 190,
                'short_desc'    => '12GB GDDR6X · DLSS 3 · 200W TDP',
                'specs'         => ['vram_gb' => 12, 'tdp' => 200, 'wattage' => 200],
            ],

            // ── Matične ploče ────────────────────────────────
            [
                'name'          => 'ASUS ROG Strix X670E-F',
                'slug'          => 'asus-rog-x670e-f',
                'brand'         => 'ASUS',
                'component_type'=> 'motherboard',
                'price'         => 349.00,
                'in_stock'      => true,
                'perf_score'    => 880,
                'tdmark_base'   => 0,
                'fps_base_1080' => 0,
                'short_desc'    => 'AM5 · DDR5 · PCIe 5.0 · WiFi 6E · ATX',
                'specs'         => [
                    'socket'      => 'AM5',
                    'ram_type'    => 'DDR5',
                    'form_factor' => 'ATX',
                    'max_ram_gb'  => 128,
                ],
            ],
            [
                'name'          => 'MSI MAG B650 Tomahawk',
                'slug'          => 'msi-b650-tomahawk',
                'brand'         => 'MSI',
                'component_type'=> 'motherboard',
                'price'         => 199.00,
                'in_stock'      => true,
                'perf_score'    => 700,
                'tdmark_base'   => 0,
                'fps_base_1080' => 0,
                'short_desc'    => 'AM5 · DDR5 · PCIe 5.0 · ATX',
                'specs'         => [
                    'socket'      => 'AM5',
                    'ram_type'    => 'DDR5',
                    'form_factor' => 'ATX',
                ],
            ],

            // ── RAM ─────────────────────────────────────────
            [
                'name'          => 'Corsair Vengeance 32GB DDR5-6000',
                'slug'          => 'corsair-vengeance-32gb-ddr5',
                'brand'         => 'Corsair',
                'component_type'=> 'ram',
                'price'         => 129.00,
                'in_stock'      => true,
                'perf_score'    => 750,
                'tdmark_base'   => 0,
                'fps_base_1080' => 0,
                'short_desc'    => '32GB (2×16) · DDR5-6000 · CL36 · XMP 3.0',
                'specs'         => [
                    'ram_type'   => 'DDR5',
                    'capacity_gb'=> 32,
                    'speed_mhz'  => 6000,
                ],
            ],
            [
                'name'          => 'G.Skill Trident Z5 64GB DDR5-6400',
                'slug'          => 'gskill-trident-z5-64gb',
                'brand'         => 'G.Skill',
                'component_type'=> 'ram',
                'price'         => 229.00,
                'in_stock'      => true,
                'perf_score'    => 820,
                'tdmark_base'   => 0,
                'fps_base_1080' => 0,
                'short_desc'    => '64GB (2×32) · DDR5-6400 · CL32 · XMP 3.0',
                'specs'         => [
                    'ram_type'   => 'DDR5',
                    'capacity_gb'=> 64,
                    'speed_mhz'  => 6400,
                ],
            ],

            // ── Kućišta ─────────────────────────────────────
            [
                'name'          => 'Lian Li O11 Dynamic EVO',
                'slug'          => 'lian-li-o11-dynamic-evo',
                'brand'         => 'Lian Li',
                'component_type'=> 'case',
                'price'         => 159.00,
                'in_stock'      => true,
                'perf_score'    => 900,
                'tdmark_base'   => 0,
                'fps_base_1080' => 0,
                'short_desc'    => 'Mid-Tower · ATX · Tempered Glass · E-ATX podrška',
                'specs'         => [
                    'form_factor'    => 'Mid-Tower',
                    'max_mobo'       => 'E-ATX',
                    'max_gpu_mm'     => 420,
                    'max_cooler_mm'  => 167,
                ],
            ],
            [
                'name'          => 'Fractal Design Torrent',
                'slug'          => 'fractal-torrent',
                'brand'         => 'Fractal Design',
                'component_type'=> 'case',
                'price'         => 189.00,
                'in_stock'      => true,
                'perf_score'    => 880,
                'tdmark_base'   => 0,
                'fps_base_1080' => 0,
                'short_desc'    => 'Mid-Tower · ATX · Odličan airflow · 2x180mm fan',
                'specs'         => [
                    'form_factor'   => 'Mid-Tower',
                    'max_mobo'      => 'E-ATX',
                    'max_gpu_mm'    => 461,
                    'max_cooler_mm' => 188,
                ],
            ],

            // ── CPU Hlađenje ─────────────────────────────────
            [
                'name'          => 'NZXT Kraken Elite 360',
                'slug'          => 'nzxt-kraken-elite-360',
                'brand'         => 'NZXT',
                'component_type'=> 'cpu_cooler',
                'price'         => 229.00,
                'in_stock'      => true,
                'perf_score'    => 920,
                'tdmark_base'   => 0,
                'fps_base_1080' => 0,
                'short_desc'    => '360mm AIO · LCD displej · AM5/LGA1700',
                'specs'         => [
                    'type'         => 'AIO',
                    'radiator_mm'  => 360,
                    'socket'       => 'AM5,LGA1700,LGA1200',
                    'max_tdp'      => 400,
                ],
            ],
            [
                'name'          => 'Noctua NH-D15',
                'slug'          => 'noctua-nh-d15',
                'brand'         => 'Noctua',
                'component_type'=> 'cpu_cooler',
                'price'         => 99.00,
                'in_stock'      => true,
                'perf_score'    => 860,
                'tdmark_base'   => 0,
                'fps_base_1080' => 0,
                'short_desc'    => 'Vazdušno · Dual Tower · AM5/LGA1700 · 165mm visina',
                'specs'         => [
                    'type'        => 'Air',
                    'height_mm'   => 165,
                    'socket'      => 'AM5,LGA1700',
                    'max_tdp'     => 300,
                ],
            ],

            // ── Ventilatori ──────────────────────────────────
            [
                'name'          => 'Lian Li UNI FAN SL120 (3-pack)',
                'slug'          => 'lian-li-uni-fan-sl120-3pack',
                'brand'         => 'Lian Li',
                'component_type'=> 'case_fan',
                'price'         => 89.00,
                'in_stock'      => true,
                'perf_score'    => 850,
                'tdmark_base'   => 0,
                'fps_base_1080' => 0,
                'short_desc'    => '3× 120mm · ARGB · Daisy-chain · 2000 RPM max',
                'specs'         => [
                    'size_mm'  => 120,
                    'count'    => 3,
                    'rgb'      => true,
                    'max_rpm'  => 2000,
                ],
            ],
            [
                'name'          => 'be quiet! Silent Wings 4 140mm (3-pack)',
                'slug'          => 'bequiet-silent-wings-4-140-3pack',
                'brand'         => 'be quiet!',
                'component_type'=> 'case_fan',
                'price'         => 69.00,
                'in_stock'      => true,
                'perf_score'    => 800,
                'tdmark_base'   => 0,
                'fps_base_1080' => 0,
                'short_desc'    => '3× 140mm · Tihi · 1550 RPM max · Bez RGB',
                'specs'         => [
                    'size_mm'  => 140,
                    'count'    => 3,
                    'rgb'      => false,
                    'max_rpm'  => 1550,
                ],
            ],

            // ── Skladištenje ─────────────────────────────────
            [
                'name'          => 'WD Black SN850X 2TB NVMe',
                'slug'          => 'wd-black-sn850x-2tb',
                'brand'         => 'Western Digital',
                'component_type'=> 'storage',
                'price'         => 149.00,
                'in_stock'      => true,
                'perf_score'    => 950,
                'tdmark_base'   => 0,
                'fps_base_1080' => 0,
                'short_desc'    => '2TB · PCIe 4.0 NVMe · 7300MB/s čitanje · M.2 2280',
                'specs'         => [
                    'capacity_gb'   => 2000,
                    'interface'     => 'PCIe 4.0 NVMe',
                    'read_mbs'      => 7300,
                    'write_mbs'     => 6600,
                    'form_factor'   => 'M.2 2280',
                ],
            ],
            [
                'name'          => 'Samsung 990 Pro 1TB NVMe',
                'slug'          => 'samsung-990-pro-1tb',
                'brand'         => 'Samsung',
                'component_type'=> 'storage',
                'price'         => 109.00,
                'in_stock'      => true,
                'perf_score'    => 900,
                'tdmark_base'   => 0,
                'fps_base_1080' => 0,
                'short_desc'    => '1TB · PCIe 4.0 NVMe · 7450MB/s čitanje · M.2 2280',
                'specs'         => [
                    'capacity_gb'   => 1000,
                    'interface'     => 'PCIe 4.0 NVMe',
                    'read_mbs'      => 7450,
                    'write_mbs'     => 6900,
                    'form_factor'   => 'M.2 2280',
                ],
            ],

            // ── Napajanje ────────────────────────────────────
            [
                'name'          => 'Corsair RM1000x',
                'slug'          => 'corsair-rm1000x',
                'brand'         => 'Corsair',
                'component_type'=> 'psu',
                'price'         => 189.00,
                'in_stock'      => true,
                'perf_score'    => 900,
                'tdmark_base'   => 0,
                'fps_base_1080' => 0,
                'short_desc'    => '1000W · 80+ Gold · Modularno · ATX 3.0',
                'specs'         => [
                    'wattage'    => 1000,
                    'efficiency' => '80+ Gold',
                    'modular'    => true,
                ],
            ],
            [
                'name'          => 'be quiet! Straight Power 12 850W',
                'slug'          => 'bequiet-straight-power-12-850w',
                'brand'         => 'be quiet!',
                'component_type'=> 'psu',
                'price'         => 149.00,
                'in_stock'      => true,
                'perf_score'    => 870,
                'tdmark_base'   => 0,
                'fps_base_1080' => 0,
                'short_desc'    => '850W · 80+ Platinum · Modularno · Tiho',
                'specs'         => [
                    'wattage'    => 850,
                    'efficiency' => '80+ Platinum',
                    'modular'    => true,
                ],
            ],
        ];

        foreach ($products as $data) {
            BuilderProduct::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }

        $this->command->info('BuilderProduct seeder završen — ' . count($products) . ' komponenti ubačeno.');
    }
}