<!-- modal de création d'un utilisateur -->
<div class="modal fade" id="createOrder" tabindex="-1" aria-labelledby="createOrderLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="createOrderLabel">Créer une nouvelle commande</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('orders.create')}}" method="post">
            @csrf
            <label>Prenom : </label>
            <input type="text" name="name" class="form-control">
            <br>
            <label>Nom :</label>
            <input type="text" name="lastname" class="form-control">
            <br>
            <label>Civilié :</label>
            <select name="civilite"class="form-control">
                <option value="homme">Homme</option>
                <option value="femme">Femme</option>
            </select>
            <label>Adresse mail : </label>
            <input type="email" name="email" class="form-control">
            <br>
            <label>Numéro de Téléphone : </label>
            <input type="text" name="phone_number" class="form-control">
            <br>
            <label>Adresse :</label>
            <input type="text" name="address" class="form-control">
            <br>
            <label>Code postal :</label>
            <input type="text" name="postal_code" class="form-control">
            <br>
            <label>Entreprise :</label>
            <input type="text" name="entreprise" class="form-control">
            <br>
            <label>Siret :</label>
            <input type="text" name="siret" class="form-control">
            <br>
            <button type="submit" class="btn btn-primary">Valider</button>
          </form>
        </div>
      </div>
    </div>
  </div>
