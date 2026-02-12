<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommodityRequest extends FormRequest
{
    protected $errorBag = 'store';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'commodity_acquisition_id' => 'required|numeric|exists:commodity_acquisitions,id',
            'commodity_location_id'   => 'required|numeric|exists:commodity_locations,id',
            'jenis_barang_id'         => 'required|numeric|exists:jenis_barang,id', // ✅ baru
            'gedung_id'               => 'required|numeric|exists:gedung,id',       // ✅ baru
            'name'                    => 'required|string|max:255',
            'brand'                   => 'nullable|string|max:255',
            'material'                => 'nullable|string|max:255',
            'date_of_purchase'        => 'required|string|max:50',
            'condition'               => 'required|in:1,2,3',
            'quantity'                => 'required|numeric|digits_between:1,10',
            'price'                   => 'required|numeric|digits_between:1,10',
            'price_per_item'          => 'required|numeric|digits_between:1,10',
            'note'                    => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'commodity_acquisition_id.required' => 'Kolom asal perolehan wajib diisi!',
            'commodity_acquisition_id.numeric'  => 'Kolom asal perolehan yang dipilih tidak valid!',
            'commodity_acquisition_id.exists'   => 'Kolom asal perolehan yang dipilih tidak valid!',

            'commodity_location_id.required' => 'Kolom lokasi barang wajib diisi!',
            'commodity_location_id.numeric'  => 'Kolom lokasi barang yang dipilih tidak valid!',
            'commodity_location_id.exists'   => 'Kolom lokasi barang yang dipilih tidak valid!',

            'jenis_barang_id.required' => 'Kolom jenis barang wajib diisi!',
            'jenis_barang_id.numeric'  => 'Kolom jenis barang yang dipilih tidak valid!',
            'jenis_barang_id.exists'   => 'Kolom jenis barang yang dipilih tidak valid!',

            'gedung_id.required' => 'Kolom gedung wajib diisi!',
            'gedung_id.numeric'  => 'Kolom gedung yang dipilih tidak valid!',
            'gedung_id.exists'   => 'Kolom gedung yang dipilih tidak valid!',

            'name.required' => 'Kolom nama barang wajib diisi!',
            'name.string'   => 'Kolom nama barang harus berupa karakter!',
            'name.max'      => 'Kolom nama barang maksimal :max karakter!',

            'date_of_purchase.required' => 'Kolom tahun pembelian wajib diisi!',

            'condition.required' => 'Kolom kondisi wajib diisi!',
            'condition.in'       => 'Kolom kondisi yang dipilih tidak valid!',

            'quantity.required'        => 'Kolom kuantitas wajib diisi!',
            'quantity.numeric'         => 'Kolom kuantitas harus berupa angka!',
            'quantity.digits_between'  => 'Kolom kuantitas harus memiliki antara :min dan :max digit!',

            'price.required'        => 'Kolom harga wajib diisi!',
            'price.numeric'         => 'Kolom harga harus berupa angka!',
            'price.digits_between'  => 'Kolom harga harus memiliki antara :min dan :max digit!',

            'price_per_item.required'        => 'Kolom harga satuan wajib diisi!',
            'price_per_item.numeric'         => 'Kolom harga satuan harus berupa angka!',
            'price_per_item.digits_between'  => 'Kolom harga satuan harus memiliki antara :min dan :max digit!',

            'note.string' => 'Kolom catatan harus berupa teks!',
            'note.max'    => 'Kolom catatan maksimal :max karakter!',
        ];
    }
}
