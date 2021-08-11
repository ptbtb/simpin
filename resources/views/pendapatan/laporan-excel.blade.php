<style>
    table{
        border-collapse: collapse;
    }
    td{
        padding: 0 3px;
    }
</style>
@php
    $month = ARR_BULAN;
@endphp
<table style="min-width: 100%" border="1">
    <tr>
        <td colspan="18" style="text-align: center; font-weight: bold">
            Laporan Simpanan
        </td>
    </tr>
    <tr>
        <td rowspan="2" style="text-align: center; font-weight: bold">Bulan</td>
        <td colspan="2" style="text-align: center; font-weight: bold">Simpanan 2021</td>
        <td style="width: 3%"></td>
        <td colspan="14" style="text-align: center; font-weight: bold">Penerimaan Simpanan 2021</td>
    </tr>
    <tr>
        <td style="text-align: center; font-weight: bold">Penerimaan</td>
        <td style="text-align: center; font-weight: bold">Pengambilan</td>
        <td></td>
        <td style="text-align: center; font-weight: bold">Jenis Simpanan</td>
        @foreach ($month as $key => $value)
            <td style="text-align: center; font-weight: bold">{{ Str::substr($value, 0, 3) }}</td>
        @endforeach
        <td style="text-align: center; font-weight: bold">Grand Total</td>
    </tr>
    @foreach ($month as $key => $value)
        <tr>
            <td style="text-align: center">{{ $key }}</td>
            <td style="text-align: right">
                {{ (isset($simpananPerbulan[$key]))? $simpananPerbulan[$key]:'' }}
            </td>
            <td style="text-align: right">
                {{ (isset($penarikanPerbulan[$key]))? $penarikanPerbulan[$key]:'' }}
            </td>
            <td></td>
            @if ($key < 6)
                <td>{{ ucwords(strtolower($listJenisSimpanan[$key-1]->name)) }}</td>
                @php
                    $id = $listJenisSimpanan[$key-1]->id;
                @endphp
                @foreach ($month as $k => $value)
                    <td style="text-align: right">
                        {{ (isset($simpananPerjenis[$id][$k]))? $simpananPerjenis[$id][$k]:'' }}
                    </td>
                @endforeach
                <td style="text-align: right;">
                    {{ (isset($simpananPerjenis[$id]))? $simpananPerjenis[$id]->sum():'' }}
                </td>
            @elseif($key < 7)
                <td style="text-align: center; font-weight: bold">Grand Total</td>
                @foreach ($month as $k => $v)
                    <td style="text-align: right;">
                        {{ (isset($simpananPerbulan[$k]))? $simpananPerbulan[$k]:'' }}
                    </td>
                @endforeach
                <td style="text-align: right;">
                    {{ (isset($simpananPerbulan))? $simpananPerbulan->sum():'' }}
                </td>
            @elseif($key < 8)
                <td style="text-align: center; font-weight: bold">Jenis Simpanan</td>
                @foreach ($month as $k => $v)
                    <td style="text-align: center; font-weight: bold">{{ Str::substr($v, 0, 3) }}</td>
                @endforeach
                <td style="text-align: center; font-weight: bold">Grand Total</td>
            @else
                <td>{{ ucwords(strtolower($listJenisSimpanan[$key-8]->name)) }}</td>
                @php
                    $id = $listJenisSimpanan[$key-8]->id;
                @endphp
                @foreach ($month as $k => $v)
                    <td style="text-align: right">
                        {{ (isset($penarikanPerjenis[$id][$k]))? $penarikanPerjenis[$id][$k]:'' }}
                    </td>
                @endforeach
                <td style="text-align: right">
                    {{ (isset($penarikanPerjenis[$id]))? $penarikanPerjenis[$id]->sum():'' }}
                </td>
            @endif
        </tr>
        @if ($key == 6)
            <tr>
                <td colspan="3" style="text-align: center; font-weight: bold"></td>
                <td style="width: 3%"></td>
                <td colspan="14" style="text-align: center; font-weight: bold">Penarikan Simpanan 2021</td>
            </tr>
        @endif
    @endforeach
    <tr>
        <td style="text-align: center; font-weight: bold">Grand Total</td>
        <td style="text-align: right">
            {{ $simpananPerbulan->sum() }}
        </td>
        <td style="text-align: right">
            {{ $penarikanPerbulan->sum() }}
        </td>
        <td></td>
        <td style="text-align: center; font-weight: bold">Grand Total</td>
        @foreach ($month as $k => $v)
            <td style="text-align: right">
                {{ (isset($penarikanPerbulan[$k]))? $penarikanPerbulan[$k]:'' }}
            </td>
        @endforeach
        <td style="text-align: right">
            {{ (isset($penarikanPerbulan))? $penarikanPerbulan->sum():'' }}
        </td>
    </tr>
</table>
