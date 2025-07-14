<?php

namespace controllers;
use core\Controller;
use core\Database;
use core\Session;
use Exception;
use models\Category;
use models\Notification;
use models\Product;

class ProductHandleController extends Controller
{
    private const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private const MAX_IMAGE_SIZE = 5 * 1024 * 1024; // 5MB
    private const UPLOAD_DIR = '/images/';

    public function indexAdd()
    {
        $categories = array_map(fn($c) => $c->toArray(), Category::findAll());
        $context = "add";
        $this
            ->setViewData(['title' => 'Aggiungi Prodotto', 'categories' => $categories, 'context' => $context])
            ->setLayout('main')
            ->render('aggiungi-prodotto');
    }

    public function addProduct()
    {
        $body = $this->request->getBody();
        $imageName = $this->uploadImage($_FILES['image']);
        $product = Product::create(
            2,
            $body['name'],
            (float) $body['price'],
            $body['description'],
            $imageName,
            $body['name'] . ' - ' . $body['description'],
            quantity: (int) $body['quantity']
        );
        $db = Database::getIstance();
        $db->transaction(function () use ($product, $body) {
            if (!$product->save()) {
                Session::setFlash('prodotto_non_aggiunto', 'Errore nel salvataggio del prodotto', 'error');
                throw new Exception("Errore nel salvataggio del prodotto");
            }
            if (!$product->addCategory($body['category'])) {
                Session::setFlash('prodotto_non_aggiunto', 'Errore nell\'associazione del prodotto alla categoria', 'error');
                throw new Exception("Errore nell'associazione del prodotto alla categoria");
            }
            Session::setFlash('prodotto_aggiunto', 'Prodotto aggiunto con successo', 'success');
        });
        $this->redirectToHome();
    }


    public function indexEdit()
    {
        $categories = array_map(fn($c) => $c->toArray(), Category::findAll());
        $product = Product::findById($this->request->getParam('id'));
        $context = "edit";
        $this
            ->setViewData(['title' => 'Aggiorna Prodotto', 'categories' => $categories, 'product' => $product->toArray(), 'context' => $context])
            ->setLayout('main')
            ->render('aggiungi-prodotto');
    }

    public function editProduct()
    {
        $product = Product::findById($this->request->getParam('id'));
        $body = $this->request->getBody();
        $product->name = $body['name'];
        $product->description = $body['description'];
        $product->price = (float) $body['price'];
        $product->quantity = (int) $body['quantity'];
        $this->updateImage($product, $_FILES['image']);
        $this->updateProductCategory($product, $body['category']);
        if ($product->save()) {
            Session::setFlash('prodotto_aggiornato', 'Prodotto aggiornato con successo', 'success');
        } else {
            Session::setFlash('prodotto_non_aggiornato', 'Errore nell\'aggiornamento del prodotto', 'error');
            throw new Exception("Errore nell'aggiornamento del prodotto");
        }
        $this->redirectToHome();
    }

    public function deleteProduct()
    {
        $product = Product::findByIdValid($this->request->getParam('id'));
        if (!$product) {
            Session::setFlash('prodotto_non_trovato', 'Prodotto non trovato', 'error');
            $this->renderJson(['success' => false, 'message' => 'Prodotto non trovato'], 404);
            return;
        }
        if ($product->delete()) {
            Session::setFlash('prodotto_rimosso', 'Prodotto rimosso con successo', 'success');
            Notification::notifyProductQuarantine($product->id);
            $this->renderJson(['success' => true, 'message' => 'Prodotto rimosso con successo']);
        } else {
            Session::setFlash('prodotto_non_rimosso', 'Errore nella rimozione del prodotto', 'error');
            $this->renderJson(['success' => false, 'message' => 'Errore nella rimozione del prodotto'], 500);
        }
    }

    public function reloadProduct()
    {
        error_log("ProductHandleController: reloadProduct called");
        $product = Product::findById($this->request->getParam('id'));
        error_log("ProductHandleController: product = " . print_r($product, true));
        if (!$product) {
            Session::setFlash('prodotto_non_trovato', 'Prodotto non trovato', 'error');
            $this->renderJson(['success' => false, 'message' => 'Prodotto non trovato'], 404);
            return;
        }
        $product->valid = 1;
        if ($product->save()) {
            Session::setFlash('prodotto_ripubblicato', 'Prodotto ripubblicato con successo', 'success');
            $this->renderJson(['success' => true, 'message' => 'Prodotto ripubblicato con successo']);
        } else {
            Session::setFlash('prodotto_non_ripubblicato', 'Errore nella ripubblicazione del prodotto', 'error');
            $this->renderJson(['success' => false, 'message' => 'Errore nella ripubblicazione del prodotto'], 500);
        }
    }


    private function updateImage(Product $product, ?array $file): void
    {
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $oldImage = $product->image_name;
            $product->image_name = $this->uploadImage($file);
            $this->deleteOldImage($oldImage);
        }
    }

    private function updateProductCategory(Product $product, string $newCategoryId): void
    {
        foreach ($product->getCategories() as $category) {
            $product->removeCategory($category->id);
        }
        $product->addCategory($newCategoryId);
    }

    private function validateImage(array $file): void
    {
        if (!in_array($file['type'], self::ALLOWED_IMAGE_TYPES)) {
            throw new Exception("Tipo di file non supportato. Usa JPEG, PNG, GIF o WebP");
        }
        if ($file['size'] > self::MAX_IMAGE_SIZE) {
            throw new Exception("File troppo grande (massimo 5MB)");
        }
    }

    private function uploadImage($file): string
    {
        $this->validateImage($file);
        // Generazione nome univoco
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('product_') . '.' . $extension;
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_DIR;
        $uploadPath = $uploadDir . $fileName;
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new Exception("Errore nel caricamento dell'immagine");
        }
        return $fileName;
    }

    private function deleteOldImage(string $oldImage): void
    {
        $imagePath = $_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_DIR . $oldImage;
        if (file_exists(filename: $imagePath)) {
            unlink($imagePath);
        }
    }


    private function redirectToHome(): void
    {
        header("Location: /");
        exit;
    }
}