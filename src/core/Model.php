<?php

namespace core;

interface Model
{
  /**
   * Trova tutte le istanze del modello.
   */
  public static function findAll();

  /**
   * Trova un'istanza del modello tramite il suo ID.
   */
  public static function findById($id);

  /**
   * Salva l'istanza corrente del modello (inserimento).
   */
  public function save();

  /**
   * Elimina l'istanza corrente del modello dal database.
   */
  public function delete();
}
