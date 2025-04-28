<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
  public function index(Request $request)
  {
    // Listar reservas (com filtro por status, data, etc. se quiser)
  }

  public function store(StoreReservationRequest $request)
  {
    // Criar a reserva (vincular hóspede + quartos)
  }

  public function show($id)
  {
    // Buscar uma reserva pelo ID com guests + rooms + payments + statuses
  }

  public function update(UpdateReservationRequest $request, $id)
  {
    // Atualizar status, datas ou informações da reserva
  }

  public function destroy($id)
  {
    // Cancelar ou excluir a reserva (soft delete se tiver `SoftDeletes`)
  }
}
