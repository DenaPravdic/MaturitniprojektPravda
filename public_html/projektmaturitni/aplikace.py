import tkinter as tk
from tkinter import messagebox, PhotoImage
import mysql.connector
import os


def connect_db():
    return mysql.connector.connect(
        host="dbs.spskladno.cz",
        user="student18",
        password="spsnet",
        database="vyuka18"
    )


def check_date():
    date = entry.get().strip()
    db = connect_db()
    cursor = db.cursor(dictionary=True)
    cursor.execute("SELECT spz, model, username, barva, potvrzeno FROM `1auta` WHERE datum_navstevy = %s", (date,))
    results = cursor.fetchall()
    db.close()

    if results:
        display_text = "\n".join([
            f"SPZ: {r['spz']}, Model: {r['model']}, Uživatel: {r['username']}, Barva: {r['barva']} - {'✅' if r['potvrzeno'] else '❓'}"
            for r in results
        ])
        result_label.config(text=display_text, fg="white")
    else:
        result_label.config(text="Žádné návštěvy nenalezeny.", fg="red")


def confirm_visit():
    date = entry.get().strip()
    db = connect_db()
    cursor = db.cursor()
    cursor.execute("UPDATE `1auta` SET potvrzeno = 1 WHERE datum_navstevy = %s", (date,))
    db.commit()
    db.close()

    if cursor.rowcount > 0:
        messagebox.showinfo("Potvrzení", f"Návštěva na {date} byla potvrzena ✅.")
        check_date()  
    else:
        messagebox.showwarning("Chyba", "Žádná návštěva nebyla nalezena.")


def cancel_visit():
    date = entry.get().strip()
    db = connect_db()
    cursor = db.cursor()
    cursor.execute("DELETE FROM `1auta` WHERE datum_navstevy = %s", (date,))
    db.commit()
    db.close()

    if cursor.rowcount > 0:
        messagebox.showinfo("Zrušení", f"Návštěvy na {date} byly odstraněny.")
        result_label.config(text="")
    else:
        messagebox.showwarning("Chyba", "Žádná návštěva nebyla nalezena.")


def show_upcoming_visits():
    db = connect_db()
    cursor = db.cursor(dictionary=True)
    cursor.execute("SELECT datum_navstevy, spz, model, username, potvrzeno FROM `1auta` WHERE datum_navstevy >= CURDATE() ORDER BY datum_navstevy ASC")
    results = cursor.fetchall()
    db.close()

    if results:
        visits_text = "\n".join([
            f"{r['datum_navstevy']} - {r['spz']} ({r['model']}) - {r['username']} - {'✅' if r['potvrzeno'] else '❓'}"
            for r in results
        ])
        messagebox.showinfo("Nadcházející návštěvy", visits_text)
    else:
        messagebox.showinfo("Nadcházející návštěvy", "Žádné nadcházející návštěvy.")


root = tk.Tk()
root.title("Správa návštěv")
root.geometry("500x500")  
root.configure(bg="#2E2E2E")



tk.Label(root, text="Vyhledávání návštěv podle data", font=("Arial", 16, "bold"), bg="#1C1C1C", fg="white", pady=10).pack(fill="x")


tk.Label(root, text="Zadejte datum návštěvy (YYYY-MM-DD):", font=("Arial", 12), bg="#2E2E2E", fg="white").pack(pady=5)
entry = tk.Entry(root, font=("Arial", 12))
entry.pack(pady=5)


tk.Button(root, text="Vyhledat návštěvu", font=("Arial", 12), command=check_date, bg="#555555", fg="white").pack(pady=5)
tk.Button(root, text="Odstranit návštěvu", font=("Arial", 12), command=cancel_visit, bg="#AA4444", fg="white").pack(pady=5)
tk.Button(root, text="Potvrdit objednávku", font=("Arial", 12), command=confirm_visit, bg="#4CAF50", fg="white").pack(pady=5)
tk.Button(root, text="Zobrazit nadcházející návštěvy", font=("Arial", 12), command=show_upcoming_visits, bg="#444444", fg="white").pack(pady=5)


result_label = tk.Label(root, text="", font=("Arial", 12), bg="#2E2E2E", fg="white", justify="left")
result_label.pack(pady=10)

root.mainloop()

