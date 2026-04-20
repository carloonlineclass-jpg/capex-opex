<?php

namespace Database\Seeders;

use App\Models\Acquisition;
use App\Models\Allocation;
use App\Models\Department;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

function bcryptSecure(string $value): string
{
    return Hash::driver('bcrypt')->make($value, ['rounds' => (int) config('security.bcrypt_rounds', 12)]);
}

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $it = Department::updateOrCreate(['code' => 'IT'], ['name' => 'IT Department', 'capex_limit' => 10, 'opex_limit' => 200]);
        $acct = Department::updateOrCreate(['code' => 'ACC'], ['name' => 'Accounting Office', 'capex_limit' => 8, 'opex_limit' => 150]);
        $amo = Department::updateOrCreate(['code' => 'AMO'], ['name' => 'Asset Management Office', 'capex_limit' => 0, 'opex_limit' => 300]);

        User::updateOrCreate(
            ['email' => 'admin@nuclark.local'],
            ['department_id' => $amo->id, 'name' => 'Asset Management Admin', 'password' => bcryptSecure('admin123'), 'role' => 'admin', 'approver_type' => null, 'is_approved' => true, 'approved_at' => now(), 'email_verified_at' => now()]
        );
        $dean = User::updateOrCreate(
            ['email' => 'dean@nuclark.local'],
            ['department_id' => $acct->id, 'name' => 'College Dean Approver', 'password' => bcryptSecure('dean12345'), 'role' => 'approver', 'approver_type' => 'dean', 'is_approved' => true, 'approved_at' => now(), 'email_verified_at' => now()]
        );
        $executive = User::updateOrCreate(
            ['email' => 'exec@nuclark.local'],
            ['department_id' => $acct->id, 'name' => 'Executive Director', 'password' => bcryptSecure('exec12345'), 'role' => 'approver', 'approver_type' => 'executive', 'is_approved' => true, 'approved_at' => now(), 'email_verified_at' => now()]
        );
        $requestor = User::updateOrCreate(
            ['email' => 'requestor@nuclark.local'],
            ['department_id' => $acct->id, 'name' => 'Sample Requestor', 'password' => bcryptSecure('request123'), 'role' => 'requestor', 'approver_type' => null, 'is_approved' => true, 'approved_at' => now(), 'email_verified_at' => now()]
        );

        $electronics = ItemCategory::updateOrCreate(['name' => 'Electronics'], ['description' => 'Monitors, system units, peripherals']);
        $furniture = ItemCategory::updateOrCreate(['name' => 'Furniture'], ['description' => 'Campus chairs and tables']);
        $office = ItemCategory::updateOrCreate(['name' => 'Office Supplies'], ['description' => 'Bond paper, pens, folders and daily consumables']);

        $monitor = Item::updateOrCreate(['item_code' => '400101-1'], [
            'category_id' => $electronics->id,
            'name' => '24-inch Monitor', 'item_type' => 'CAPEX', 'description' => 'Assigned to laboratory room', 'specifications' => '24-inch LED monitor',
            'quantity' => 1, 'unit' => 'pc', 'unit_price' => 9500, 'brand' => 'Generic', 'availability_status' => 'Available', 'low_stock_threshold' => 0, 'qr_value' => '400101-1', 'room_assigned' => '718', 'is_active' => true
        ]);
        Item::updateOrCreate(['item_code' => '300101-1'], [
            'category_id' => $furniture->id,
            'name' => 'Jasmine Chair', 'item_type' => 'CAPEX', 'description' => 'Student chair with QR room tracking', 'specifications' => 'School chair',
            'quantity' => 1, 'unit' => 'pc', 'unit_price' => 2500, 'brand' => 'Generic', 'availability_status' => 'Available', 'low_stock_threshold' => 0, 'qr_value' => '300101-1', 'room_assigned' => '719', 'is_active' => true
        ]);

        $opexItems = [
            ['CERTIFICATE HOLDER','A4, 9" x 12"; NAVY BLUE','piece',36.00,'ADVENTURER','Available',25],
            ['CLIP, BINDER CLIP','1 5/8", BLACK; 12 pcs / box','box',43.20,'NO BRAND','Available',18],
            ['CLIP, BINDER CLIP','2", BLACK; 12 pcs / box','box',63.00,'NO BRAND','Available',15],
            ['CLIP, PAPER CLIP','BIG; VINYL COATED','box',22.00,'PRINCE','Out of Stock',0],
            ['CLIP, PAPER CLIP','SMALL; VINYL COATED','box',10.50,'PRINCE','Out of Stock',0],
            ['CORRECTION TAPE','J-863 5mm x 8m','piece',16.00,'JOY','Out of Stock',0],
            ['ENVELOPE','LONG; BROWN','piece',1.60,'NO BRAND','Available',100],
            ['ENVELOPE, EXPANDABLE','LONG, WITH GARTER; BLUE','piece',12.00,'COSMIC','Available',35],
            ['FILE DIVIDER','A4, 5s / pack; ASSORTED COLOR','pack',20.00,'NO BRAND','Available',20],
            ['FOLDER','LONG; WHITE','piece',4.80,'ASIAN','Available',50],
            ['FOLDER','SHORT; WHITE','piece',4.20,'ASIAN','Limited Stock',5],
            ['FOLDER, ARCH FILE','A4; 2 RINGS, 3"; BLUE','piece',85.00,'SNOWMAN','Available',12],
            ['FOLDER, ARCH FILE','LONG; 2 RINGS, 3"; BLUE','piece',90.00,'SNOWMAN','Available',12],
            ['FOLDER, EXPANDABLE','LONG; BLUE','piece',16.00,'PIX','Available',15],
            ['FOLDER, PLASTIC JACKET','LONG; CLEAR','piece',10.00,'ADVENTURER','Available',30],
            ['GLUE','130g, LIQUID WHITE','bottle',56.00,"ELMER'S",'Available',14],
            ['IN AND OUT TRAY','3-LAYER, METAL; BLACK','piece',550.00,'NO BRAND','Limited Stock',3],
            ['INK, STAMP PAD','PURPLE','bottle',14.50,'LCT','Available',10],
            ['LAMINATING FILM','A4, 100s per box; 125 MICRON','box',530.00,'NO BRAND','Available',8],
            ['MAGAZINE BOX','SINGLE; NAVY BLUE','piece',90.00,'NO BRAND','Available',10],
            ['PAPER, BOND PAPER','LONG (8.5" x 13"); 500s / ream','ream',190.00,'A PLUS','Limited Stock',5],
            ['PAPER, BOND PAPER','A4 (8.27" x 11.69"); 500s / ream','ream',171.00,'ADVANCE','Limited Stock',5],
            ['PAPER, BOND PAPER','SHORT / LETTER (8.5" x 11"); 500s / ream','ream',160.00,'A PLUS','Limited Stock',5],
            ['PAPER, SPECIALTY BOARD','A4, 10s per pack; PALE CREAM','pack',32.00,'NO BRAND','Limited Stock',4],
            ['PAPER, STICKER','A4, 10s per pack; WHITE MATTE','pack',32.00,'NO BRAND','Available',12],
            ['PEN, MARKER, PERMANENT','REFILLABLE; BLACK','piece',29.50,'PILOT','Limited Stock',4],
            ['PEN, MARKER, WHITEBOARD','REFILLABLE; BLACK','piece',46.00,'PILOT','Out of Stock',0],
            ['PENCIL','#2','piece',8.00,'MONGOL','Out of Stock',0],
            ['PUNCHER','BIG','piece',160.00,'HBW','Available',7],
            ['PUSHPIN','100s per box','box',30.00,'NO BRAND','Available',11],
            ['RECORD BOOK','500 pages','piece',85.00,'ASIAN','Available',6],
            ['RECORD BOOK','300 pages','piece',62.00,'ASIAN','Available',9],
            ['RULER','12 inches','piece',7.00,'PRINCE','Limited Stock',4],
            ['SCISSORS','8 1/4"','piece',37.00,'HBW','Limited Stock',4],
            ['STAMP PAD','WITH INK, BLUE; #2','piece',32.00,'LCT','Available',8],
            ['STAMP, MANUAL DATER','4mm','piece',36.00,'JOY','Limited Stock',2],
            ['STAPLE WIRE','#35-5M, 26/6; 5000s','box',74.00,'MAX','Available',9],
            ['STAPLER','HD-50/50R; WITH REMOVER','piece',390.00,'MAX','Limited Stock',2],
            ['STICKY NOTES','3" x 3"','pad',15.00,'NO BRAND','Out of Stock',0],
            ['TAPE DISPENSER','BIG','piece',87.00,'NO BRAND','Available',5],
            ['TAPE, CLEAR','1in. x 50y','roll',12.00,'APPLE','Available',16],
            ['TAPE, DOUBLE-SIDED','1in. WITHOUT FOAM','roll',20.50,'NO BRAND','Available',8],
            ['TAPE, MASKING','1in','roll',32.00,'CROCODILE','Available',10],
        ];

        foreach ($opexItems as $index => [$name, $specs, $unit, $price, $brand, $status, $qty]) {
            $code = 'OPEX-AMO-'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);
            Item::updateOrCreate(['item_code' => $code], [
                'category_id' => $office->id,
                'name' => $name,
                'item_type' => 'OPEX',
                'description' => 'Current AMO supply item imported from the provided inventory screenshots.',
                'specifications' => $specs,
                'quantity' => $qty,
                'unit' => $unit,
                'unit_price' => $price,
                'brand' => $brand,
                'availability_status' => $status,
                'low_stock_threshold' => 5,
                'qr_value' => null,
                'room_assigned' => null,
                'is_active' => true,
            ]);
        }

        Allocation::updateOrCreate(['department_id' => $it->id, 'item_type' => 'CAPEX'], ['max_quantity' => 3, 'period_label' => 'Yearly']);
        Allocation::updateOrCreate(['department_id' => $it->id, 'item_type' => 'OPEX'], ['max_quantity' => 50, 'period_label' => 'Monthly']);
        Allocation::updateOrCreate(['department_id' => $acct->id, 'item_type' => 'CAPEX'], ['max_quantity' => 2, 'period_label' => 'Yearly']);
        Allocation::updateOrCreate(['department_id' => $acct->id, 'item_type' => 'OPEX'], ['max_quantity' => 40, 'period_label' => 'Monthly']);

        $supplier = Supplier::updateOrCreate(['name' => 'TechSource Trading'], [
            'contact_person' => 'Maria Santos', 'email' => 'maria@techsource.local', 'phone' => '09171234567', 'address' => 'Angeles City'
        ]);
        $supplier2 = Supplier::updateOrCreate(['name' => 'OfficeHub Supply'], [
            'contact_person' => 'John Cruz', 'email' => 'sales@officehub.local', 'phone' => '09179876543', 'address' => 'Clark, Pampanga'
        ]);

        Acquisition::updateOrCreate(['supplier_id' => $supplier->id, 'item_id' => $monitor->id, 'acquisition_date' => now()->toDateString()], [
            'quantity' => 2, 'unit_cost' => 9500, 'remarks' => 'Delivered to Asset Management before room deployment'
        ]);

        $bond = Item::where('item_code', 'OPEX-AMO-021')->first();
        $stapler = Item::where('item_code', 'OPEX-AMO-038')->first();
        if ($bond) {
            Acquisition::updateOrCreate(['supplier_id' => $supplier2->id, 'item_id' => $bond->id, 'acquisition_date' => now()->subDays(3)->toDateString()], [
                'quantity' => 20, 'unit_cost' => 190, 'remarks' => 'Office paper restock'
            ]);
        }

        $requisition = Requisition::updateOrCreate(['requisition_no' => 'REQ-SAMPLE-001'], [
            'user_id' => $requestor->id,
            'department_id' => $acct->id,
            'branch' => 'NU Clark',
            'charge_to_budget_item' => 'Office Supplies',
            'csf_no' => 'CSF-001',
            'requested_by_name' => $requestor->name,
            'checked_by_name' => $dean->name,
            'approved_by_name' => $executive->name,
            'status' => 'pending_asset_management',
            'purpose' => 'Daily operation / enrollment',
            'requested_at' => now(),
        ]);

        if ($bond) {
            RequisitionItem::updateOrCreate(['requisition_id' => $requisition->id, 'item_id' => $bond->id], [
                'quantity_requested' => 5,
                'quantity_approved' => null,
                'remarks' => 'For office printing',
            ]);
        }

        if ($stapler) {
            RequisitionItem::updateOrCreate(['requisition_id' => $requisition->id, 'item_id' => $stapler->id], [
                'quantity_requested' => 1,
                'quantity_approved' => null,
                'remarks' => 'For records and enrollment forms',
            ]);
        }
    }
}
