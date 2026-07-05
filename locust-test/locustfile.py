import random
import string
from locust import HttpUser, between, task

class UserInventarisElektronik(HttpUser):
    host = "http://127.0.0.1:8000/api"
    wait_time = between(0.1, 0.5)

    def on_start(self):
        self.id_item_baru = None

    def generate_kode(self):
        return "".join(random.choices(string.ascii_uppercase + string.digits, k=8))

    @task(5)
    def lihat_items(self):
        with self.client.get(
            "/items",
            name="Lihat Data Item",
            catch_response=True,
        ) as response:
            if response.status_code == 200:
                response.success()
            else:
                response.failure(f"Gagal lihat data: {response.status_code}")

    @task(3)
    def tambah_item(self):
        kode = self.generate_kode()
        item_data = {
            "code": f"LOC-{kode}",
            "name": f"Item Locust {kode}",
            "stock": random.randint(10, 500),
            "stock_minimum": 10,
            "purchase_price": random.randint(1000, 50000),
        }
        with self.client.post(
            "/items",
            json=item_data,
            name="Tambah Data Item",
            catch_response=True,
        ) as response:
            if response.status_code in [200, 201]:
                try:
                    data = response.json()
                    self.id_item_baru = data["data"]["id"]
                    response.success()
                except Exception as e:
                    response.failure(f"Response JSON tidak sesuai: {e}")
            else:
                response.failure(f"Gagal tambah data: {response.status_code}")

    @task(2)
    def edit_item(self):
        if not self.id_item_baru:
            return
        kode = self.generate_kode()
        item_update = {
            "name": f"Item Locust Edit {kode}",
            "stock": random.randint(10, 500),
        }
        with self.client.put(
            f"/items/{self.id_item_baru}",
            json=item_update,
            name="Edit Data Item",
            catch_response=True,
        ) as response:
            if response.status_code == 200:
                response.success()
            else:
                response.failure(f"Gagal edit data: {response.status_code}")

    @task(1)
    def hapus_item(self):
        if not self.id_item_baru:
            return
        with self.client.delete(
            f"/items/{self.id_item_baru}",
            name="Hapus Data Item",
            catch_response=True,
        ) as response:
            if response.status_code == 200:
                self.id_item_baru = None
                response.success()
            else:
                response.failure(f"Gagal hapus data: {response.status_code}")