<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * عرض العقارات حسب الدور
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // المدير يرى كل شيء
        if ($user->role === 'admin') {
            return Property::all();
        }

        // المستثمر يرى الأراضي فقط
        if ($user->role === 'investor') {
            return Property::where('type', 'Land')->get();
        }

        // المستأجر يرى كل العقارات
        if ($user->role === 'tenant') {
            return Property::all();
        }

        // المالك يرى عقاراته فقط
        if ($user->role === 'owner') {
            return Property::where('user_id', $user->id)->get();
        }

        return response()->json(['message' => 'Unauthorized role'], 403);
    }

    /**
     * إضافة عقار (المالك + المدير)
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['owner', 'admin'])) {
            return response()->json(['message' => 'You cannot add properties'], 403);
        }

        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'location' => 'required|string',
            'type' => 'required|in:Land,Apartment', // نوع العقار
        ]);

        $property = Property::create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'location' => $request->location,
            'type' => $request->type,
            'user_id' => $user->id,
        ]);

        return response()->json($property, 201);
    }

    /**
     * تعديل العقار
     */
    public function update(Request $request, Property $property)
    {
        $user = $request->user();

        // المدير يعدل أي شيء
        if ($user->role === 'admin') {
            return $this->updateProperty($request, $property);
        }

        // المالك يعدل عقاراته فقط
        if ($user->role === 'owner' && $property->user_id === $user->id) {
            return $this->updateProperty($request, $property);
        }

        return response()->json(['message' => 'You cannot edit this property'], 403);
    }

    private function updateProperty(Request $request, Property $property)
    {
        $request->validate([
            'title' => 'string',
            'description' => 'string|nullable',
            'price' => 'numeric',
            'location' => 'string',
            'type' => 'in:Land,Apartment',
        ]);

        $property->update($request->all());

        return response()->json($property);
    }

    /**
     * حذف العقار
     */
    public function destroy(Request $request, Property $property)
    {
        $user = $request->user();

        // المدير يحذف أي شيء
        if ($user->role === 'admin') {
            $property->delete();
            return response()->json(['message' => 'Property deleted']);
        }

        // المالك يحذف عقاراته فقط
        if ($user->role === 'owner' && $property->user_id === $user->id) {
            $property->delete();
            return response()->json(['message' => 'Property deleted']);
        }

        return response()->json(['message' => 'You cannot delete this property'], 403);
    }
}
