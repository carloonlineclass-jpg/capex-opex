<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class QrController extends Controller
{
    public function index(Request $request)
    {
        $capexItems = Item::where('item_type', 'CAPEX')->orderBy('name')->get();
        $selectedItem = null;
        $normalizedCode = null;
        $verifiedRoom = null;
        $verificationStatus = null;
        $verificationMessage = null;

        if ($request->filled('code')) {
            $normalizedCode = trim((string) $request->code);

            if (preg_match('#/items/(\d+)$#', $normalizedCode, $matches)) {
                $selectedItem = Item::with('category')->find($matches[1]);
            }

            if (!$selectedItem) {
                $selectedItem = Item::with('category')
                    ->where('item_code', $normalizedCode)
                    ->orWhere('qr_value', $normalizedCode)
                    ->first();
            }
        }

        if ($selectedItem && $request->filled('verify_room')) {
            $verifiedRoom = trim((string) $request->verify_room);
            $assignedRoom = trim((string) ($selectedItem->room_assigned ?? ''));

            if ($assignedRoom === '') {
                $verificationStatus = 'no-room';
                $verificationMessage = 'This asset has no assigned room yet. Update the asset record first to use location verification.';
            } elseif (strcasecmp($verifiedRoom, $assignedRoom) === 0) {
                $verificationStatus = 'matched';
                $verificationMessage = 'Asset verified successfully. The scanned asset matches its assigned room.';
            } else {
                $verificationStatus = 'mismatch';
                $verificationMessage = 'Location mismatch detected. The scanned asset does not match its assigned room.';
            }
        }

        return view('qr.index', compact(
            'capexItems',
            'selectedItem',
            'normalizedCode',
            'verifiedRoom',
            'verificationStatus',
            'verificationMessage'
        ));
    }
}
