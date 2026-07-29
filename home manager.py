import tkinter as tk
from tkinter import ttk, messagebox
import json
import os
import datetime
from tkinter import font

# GROCERY LIST MANAGER
grocery_list = []

def add_grocery_item(item_name, category, quantity):
    """Add item to grocery list"""
    grocery_list.append({"name": item_name, "category": category, "quantity": quantity})

def delete_grocery_item(index):
    """Delete item from grocery list"""
    if 0 <= index < len(grocery_list):
        grocery_list.pop(index)

def edit_grocery_item(index, item_name, category, quantity):
    """Edit existing grocery item"""
    if 0 <= index < len(grocery_list):
        grocery_list[index] = {"name": item_name, "category": category, "quantity": quantity}

def get_grocery_list():
    """Get all grocery items"""
    return grocery_list

def save_grocery_file():
    """Save grocery list to file"""
    with open("grocery_list.txt", "w") as file:
        for item in grocery_list:
            file.write(f"{item['name']} - {item['category']} - Qty: {item['quantity']}\n")

# MEAL PLANNER
class Meal:
    def __init__(self, name, ingredients, calories, category):
        self.name = name
        self.ingredients = ingredients
        self.calories = calories
        self.category = category

meals_list = []

def add_meal(name, ingredients, calories, category):
    for meal in meals_list:
        if meal.name.lower() == name.lower():
            return False

    meals_list.append(Meal(name, ingredients, calories, category))
    return True

def delete_meal(name):
    global meals_list
    meals_list = [meal for meal in meals_list if meal.name.lower() != name.lower()]

def edit_meal(index, name, ingredients, calories, category):
    """Edit existing meal"""
    if 0 <= index < len(meals_list):
        for i, meal in enumerate(meals_list):
            if i != index and meal.name.lower() == name.lower():
                return False
        meals_list[index].name = name
        meals_list[index].ingredients = ingredients
        meals_list[index].calories = calories
        meals_list[index].category = category
        return True
    return False

def save_meals():
    data = [{"name": m.name, "ingredients": m.ingredients, "calories": m.calories, "category": m.category}
            for m in meals_list]
    with open("meals.json", "w") as f:
        json.dump(data, f, indent=4)

def load_meals():
    if os.path.exists("meals.json"):
        with open("meals.json", "r") as f:
            data = json.load(f)
            meals_list.clear()
            for m in data:
                meals_list.append(Meal(m["name"], m["ingredients"], m["calories"], m["category"]))

# CHORE SCHEDULER
class Chore:
    def __init__(self, task, assignee, due_date, status="❗Pending"):
        self.task = task
        self.assignee = assignee
        self.due_date = due_date
        self.status = status

chores_list = []
next_chore_id = 1

def add_chore(task, assignee, due_date):
    global next_chore_id
    chore = Chore(task, assignee, due_date)
    chore.id = next_chore_id
    chores_list.append(chore)
    next_chore_id += 1

def get_chores():
    return chores_list

def mark_chore_complete(chore_id):
    for chore in chores_list:
        if chore.id == chore_id:
            chore.status = "✅Completed"
            return True
    return False

def delete_chore(chore_id):
    global chores_list
    chores_list = [c for c in chores_list if c.id != chore_id]

def edit_chore(chore_id, task, assignee, due_date):
    """Edit existing chore"""
    for chore in chores_list:
        if chore.id == chore_id:
            chore.task = task
            chore.assignee = assignee
            chore.due_date = due_date
            return True
    return False

def save_chores():
    """Save chores to a JSON file"""
    data = [
        {
            "id": chore.id,
            "task": chore.task,
            "assignee": chore.assignee,
            "due_date": chore.due_date,
            "status": chore.status
        }
        for chore in chores_list
    ]
    with open("chores.json", "w") as f:
        json.dump(data, f, indent=4)

def load_chores():
    """Load chores from a JSON file"""
    global chores_list, next_chore_id
    if os.path.exists("chores.json"):
        try:
            with open("chores.json", "r") as f:
                data = json.load(f)
                chores_list.clear()
                for item in data:
                    chore = Chore(item["task"], item["assignee"], item["due_date"], item["status"])
                    chore.id = item["id"]
                    chores_list.append(chore)
                if chores_list:
                    next_chore_id = max(c.id for c in chores_list) + 1
                else:
                    next_chore_id = 1
        except:
            pass


# GUI APPLICATION 
class SimpleHomeManager:
    def __init__(self, root):
        self.root = root
        self.root.title("🏠Household Management Assistant🏠")
        self.root.geometry("800x600")
        load_meals()
        load_chores()
        self.show_main_menu()

    def clear_window(self):
        for widget in self.root.winfo_children():
            widget.destroy()

    def show_main_menu(self):
        self.clear_window()
        title = tk.Label(self.root, text="🏠Household Management Assistant🏠", font=("Arial", 20, "bold"))
        title.pack(pady=20)
        tk.Button(self.root, text="Grocery List Manager", width=30, height=2, command=self.show_grocery_screen, bg="blue", fg="white", font=("Arial", 12, "bold")).pack(pady=10)
        tk.Button(self.root, text="Meal Planner", width=30, height=2, command=self.show_meal_screen, bg="green", fg="white", font=("Arial", 12, "bold")).pack(pady=10)
        tk.Button(self.root, text="Chore Scheduler", width=30, height=2, command=self.show_chore_screen, bg="orange", fg="white", font=("Arial", 12, "bold")).pack(pady=10)
        tk.Button(self.root, text="Exit Application", width=30, height=2, command=self.root.quit, bg="red", fg="white", font=("Arial", 12, "bold")).pack(pady=10)

# GROCERY LIST SCREEN 
    def show_grocery_screen(self):
        self.clear_window()
        
        header_frame = tk.Frame(self.root, bg="blue")
        header_frame.pack(fill=tk.X)
        tk.Label(header_frame, text="🛒 Grocery List Manager", font=("Arial", 18, "bold"), 
                bg="blue", fg="white", pady=10).pack()
        
        button_frame = tk.Frame(self.root)
        button_frame.pack(pady=10)
        
        tk.Button(button_frame, text="Add Item", command=self.grocery_add_item_dialog, bg="green", fg="white", width=15).pack(side=tk.LEFT, padx=5)
        tk.Button(button_frame, text="Edit Item", command=self.grocery_edit_item_dialog, bg="orange", fg="white", width=15).pack(side=tk.LEFT, padx=5)
        tk.Button(button_frame, text="Delete Item", command=self.grocery_delete_item_dialog, bg="red", fg="white", width=15).pack(side=tk.LEFT, padx=5)
        tk.Button(button_frame, text="Save to File", command=self.grocery_save, bg="purple", fg="white", width=15).pack(side=tk.LEFT, padx=5)
        tk.Button(button_frame, text="Back to Main Menu", command=self.show_main_menu, bg="gray", fg="white", width=15).pack(side=tk.LEFT, padx=5)
        
        table_frame = tk.Frame(self.root)
        table_frame.pack(fill=tk.BOTH, expand=True, padx=10, pady=10)
        
        scrollbar_y = tk.Scrollbar(table_frame)
        scrollbar_y.pack(side=tk.RIGHT, fill=tk.Y)
        
        scrollbar_x = tk.Scrollbar(table_frame, orient=tk.HORIZONTAL)
        scrollbar_x.pack(side=tk.BOTTOM, fill=tk.X)
        
        self.grocery_tree = ttk.Treeview(table_frame, columns=("No", "Item Name", "Category", "Quantity"), 
                                        show="headings", yscrollcommand=scrollbar_y.set, 
                                        xscrollcommand=scrollbar_x.set, height=15)
        
        self.grocery_tree.heading("No", text="No.")
        self.grocery_tree.heading("Item Name", text="Item Name")
        self.grocery_tree.heading("Category", text="Category")
        self.grocery_tree.heading("Quantity", text="Quantity")
        
        self.grocery_tree.column("No", width=50, anchor=tk.CENTER)
        self.grocery_tree.column("Item Name", width=200, anchor=tk.CENTER)  
        self.grocery_tree.column("Category", width=150, anchor=tk.CENTER)
        self.grocery_tree.column("Quantity", width=100, anchor=tk.CENTER)
        
        style = ttk.Style()
        style.configure("Treeview", rowheight=25, font=('Arial', 10))
        style.configure("Treeview.Heading", font=('Arial', 11, 'bold'))
        
        self.grocery_tree.pack(fill=tk.BOTH, expand=True)
        scrollbar_y.config(command=self.grocery_tree.yview)
        scrollbar_x.config(command=self.grocery_tree.xview)
        
        summary_frame = tk.Frame(self.root)
        summary_frame.pack(fill=tk.X, padx=10, pady=5)
        self.grocery_summary_label = tk.Label(summary_frame, text="", font=("Arial", 10, "bold"))
        self.grocery_summary_label.pack()
        
        self.refresh_grocery_table()

    def refresh_grocery_table(self):
        for item in self.grocery_tree.get_children():
            self.grocery_tree.delete(item)
        
        for i, item in enumerate(grocery_list, 1):
            self.grocery_tree.insert("", tk.END, values=(i, item['name'], item['category'], item['quantity']))
        
        total_items = len(grocery_list)
        self.grocery_summary_label.config(text=f"📊 Total Items: {total_items}")

    def grocery_add_item_dialog(self):
        dialog = tk.Toplevel(self.root)
        dialog.title("Add Item")
        dialog.geometry("400x300")
        dialog.transient(self.root)
        dialog.grab_set()

        tk.Label(dialog, text="Add Grocery Item", font=("Arial", 14, "bold")).pack(pady=10)

        tk.Label(dialog, text="Item Name:").pack(pady=5)
        item_entry = tk.Entry(dialog, width=30)
        item_entry.pack(pady=5)

        tk.Label(dialog, text="Category:").pack(pady=5)
        category_var = tk.StringVar(value="Fruits")
        category_menu = tk.OptionMenu(dialog, category_var, "Fruits", "Vegetables", "Meat", "Drinks", "Snacks", "Others")
        category_menu.pack(pady=5)

        tk.Label(dialog, text="Quantity:").pack(pady=5)
        qty_entry = tk.Entry(dialog, width=30)
        qty_entry.pack(pady=5)

        def add_item():
            item = item_entry.get().strip().title()
            category = category_var.get()
            qty = qty_entry.get().strip()

            if not item or not qty:
                messagebox.showerror("Error", "Please fill all fields!")
                return
            if not qty.isdigit() or not (1 <= int(qty) <= 100):
                messagebox.showerror("Error", "Quantity must be between 1 and 100!")
                return
            add_grocery_item(item, category, qty)
            self.refresh_grocery_table()
            dialog.destroy()
            messagebox.showinfo("Success", "Item added!")

        tk.Button(dialog, text="Add", command=add_item, width=20, bg="green", fg="white").pack(pady=10)

    def grocery_edit_item_dialog(self):
        """Dialog to edit an existing grocery item"""
        selection = self.grocery_tree.selection()
        
        if not selection:
            messagebox.showerror("Error", "Please select an item to edit!")
            return
        
        selected_item = self.grocery_tree.item(selection[0])
        values = selected_item['values']
        item_index = values[0] - 1 
        current_item = grocery_list[item_index]
        
        dialog = tk.Toplevel(self.root)
        dialog.title("Edit Item")
        dialog.geometry("400x300")
        dialog.transient(self.root)
        dialog.grab_set()
        
        tk.Label(dialog, text="Edit Grocery Item", font=("Arial", 14, "bold")).pack(pady=10)
        
        tk.Label(dialog, text="Item Name:").pack(pady=5)
        item_entry = tk.Entry(dialog, width=30)
        item_entry.insert(0, current_item['name'])
        item_entry.pack(pady=5)
        
        tk.Label(dialog, text="Category:").pack(pady=5)
        category_var = tk.StringVar(value=current_item['category'])
        tk.OptionMenu(dialog, category_var, "Fruits", "Vegetables", "Meat", "Drinks", "Snacks", "Others").pack(pady=5)
        
        tk.Label(dialog, text="Quantity:").pack(pady=5)
        qty_entry = tk.Entry(dialog, width=30)
        qty_entry.insert(0, current_item['quantity'])
        qty_entry.pack(pady=5)
        
        def save_edit():
            item = item_entry.get().strip().title()
            category = category_var.get()
            qty = qty_entry.get().strip()
            
            if not item or not qty:
                messagebox.showerror("Error", "Please fill all fields!")
                return
            
            if not qty.isdigit() or not (1 <= int(qty) <= 100):
                messagebox.showerror("Error", "Quantity must be between 1 and 100!")
                return
            
            edit_grocery_item(item_index, item, category, qty)
            self.refresh_grocery_table()
            dialog.destroy()
            messagebox.showinfo("Success", "Item updated successfully!")
        
        def cancel_edit():
            dialog.destroy()
        
        button_frame = tk.Frame(dialog)
        button_frame.pack(pady=20)
        
        tk.Button(button_frame, text="Save Changes", command=save_edit, bg="green", fg="white", width=15).pack(side=tk.LEFT, padx=10)
        tk.Button(button_frame, text="Cancel", command=cancel_edit, bg="gray", fg="white", width=15).pack(side=tk.LEFT, padx=10)

    def grocery_delete_item_dialog(self):
        selection = self.grocery_tree.selection()
        
        if not selection:
            messagebox.showerror("Error", "Please select an item to delete!")
            return
        
        selected_item = self.grocery_tree.item(selection[0])
        values = selected_item['values']
        item_index = values[0] - 1  
        
        if messagebox.askyesno("Confirm", f"Delete '{grocery_list[item_index]['name']}'?"):
            delete_grocery_item(item_index)
            self.refresh_grocery_table()
            messagebox.showinfo("Success", "Item deleted!")


    def grocery_save(self):
        try: 
            save_grocery_file()
            messagebox.showinfo("Success", "Grocery list saved successfully!")
        except: 
            messagebox.showerror("Error", "Could not save file!")

    # MEAL PLANNER

    def show_meal_screen(self):
        self.clear_window()
        
        header_frame = tk.Frame(self.root, bg="green")
        header_frame.pack(fill=tk.X)
        tk.Label(header_frame, text="🍽️ Meal Planner", font=("Arial", 18, "bold"), bg="green", fg="white").pack(pady=10)     

        button_frame = tk.Frame(self.root)
        button_frame.pack(pady=10)
        
        tk.Button(button_frame, text="Add Meal", command=self.meal_add_dialog, bg="green", fg="white", width=15).pack(side=tk.LEFT, padx=5)
        tk.Button(button_frame, text="Edit Meal", command=self.meal_edit_dialog, bg="orange", fg="white", width=15).pack(side=tk.LEFT, padx=5)
        tk.Button(button_frame, text="Delete Meal", command=self.meal_delete_dialog, bg="red", fg="white", width=15).pack(side=tk.LEFT, padx=5)
        tk.Button(button_frame, text="Save", command=self.meal_save, bg="purple", fg="white", width=15).pack(side=tk.LEFT, padx=5)
        tk.Button(button_frame, text="Back", command=self.show_main_menu, bg="gray", fg="white", width=15).pack(side=tk.LEFT, padx=5)
        
        table_frame = tk.Frame(self.root)
        table_frame.pack(fill=tk.BOTH, expand=True, padx=10, pady=10)
        
        scrollbar_y = tk.Scrollbar(table_frame)
        scrollbar_y.pack(side=tk.RIGHT, fill=tk.Y)
        
        scrollbar_x = tk.Scrollbar(table_frame, orient=tk.HORIZONTAL)
        scrollbar_x.pack(side=tk.BOTTOM, fill=tk.X)
        
        self.meal_tree = ttk.Treeview(table_frame, columns=("No", "Meal Name", "Category", "Calories", "Ingredients"), 
                                     show="headings", yscrollcommand=scrollbar_y.set, 
                                     xscrollcommand=scrollbar_x.set, height=15)
        
        self.meal_tree.heading("No", text="No.")
        self.meal_tree.heading("Meal Name", text="Meal Name")
        self.meal_tree.heading("Category", text="Category")
        self.meal_tree.heading("Calories", text="Calories")
        self.meal_tree.heading("Ingredients", text="Ingredients")
        
        self.meal_tree.column("No", width=50, anchor=tk.CENTER)
        self.meal_tree.column("Meal Name", width=150, anchor=tk.CENTER) 
        self.meal_tree.column("Category", width=100, anchor=tk.CENTER)
        self.meal_tree.column("Calories", width=80, anchor=tk.CENTER)
        self.meal_tree.column("Ingredients", width=300, anchor=tk.CENTER) 
        
        self.meal_tree.pack(fill=tk.BOTH, expand=True)
        scrollbar_y.config(command=self.meal_tree.yview)
        scrollbar_x.config(command=self.meal_tree.xview)
        
        summary_frame = tk.Frame(self.root)
        summary_frame.pack(fill=tk.X, padx=10, pady=5)
        self.meal_summary_label = tk.Label(summary_frame, text="", font=("Arial", 10, "bold"))
        self.meal_summary_label.pack()
        
        self.refresh_meal_table()


    def refresh_meal_table(self):
        for item in self.meal_tree.get_children():
            self.meal_tree.delete(item)
        
        for i, meal in enumerate(meals_list, 1):
            self.meal_tree.insert("", tk.END, values=(i, meal.name, meal.category, meal.calories, ", ".join(meal.ingredients)))
        
        total_meals = len(meals_list)
        self.meal_summary_label.config(text=f"📊 Total Meals: {total_meals}")


    def meal_add_dialog(self):
        dialog = tk.Toplevel(self.root)
        dialog.title("Add Meal")
        dialog.geometry("400x300")
        dialog.transient(self.root)
        dialog.grab_set()

        tk.Label(dialog, text="Add Meal", font=("Arial", 14, "bold")).pack(pady=10)

        tk.Label(dialog, text="Name:").pack()
        name_entry = tk.Entry(dialog, width=30)
        name_entry.pack()

        tk.Label(dialog, text="Ingredients (comma separated):").pack()
        ing_entry = tk.Entry(dialog, width=30)
        ing_entry.pack()

        tk.Label(dialog, text="Calories:").pack()
        cal_entry = tk.Entry(dialog, width=30)
        cal_entry.pack()

        tk.Label(dialog, text="Category:").pack()
        category_var = tk.StringVar(value="Breakfast")
        tk.OptionMenu(dialog, category_var, "Breakfast", "Lunch", "Dinner", "Snack").pack()

        def add_meal_btn():
            name = name_entry.get().strip()
            ingredients = [i.strip() for i in ing_entry.get().split(",")]
            calories = cal_entry.get().strip()
            category = category_var.get()

            if not name:
                messagebox.showerror("Error", "Name cannot be empty!")
                return
            
            if not calories.isdigit() or int(calories) > 5000:
                messagebox.showerror("Error", "Calories must be a number of ≤5000!")
                return
           
            if add_meal(name, ingredients, int(calories), category):
                self.refresh_meal_table()
                dialog.destroy()
                messagebox.showinfo("Success", "Meal added!")
            else:
                messagebox.showerror("Error", "Meal already exists!")

        tk.Button(dialog, text="Add Meal", command=add_meal_btn, width=20, bg="green", fg="white").pack(pady=10)


    def meal_edit_dialog(self):
        """Dialog to edit an existing meal"""
        selection = self.meal_tree.selection()
        
        if not selection:
            messagebox.showerror("Error", "Please select a meal to edit!")
            return
        
        selected_item = self.meal_tree.item(selection[0])
        values = selected_item['values']
        meal_index = values[0] - 1  
        
        current_meal = meals_list[meal_index]
        
        dialog = tk.Toplevel(self.root)
        dialog.title("Edit Meal")
        dialog.geometry("400x300")
        dialog.transient(self.root)
        dialog.grab_set()
        
        tk.Label(dialog, text="Edit Meal", font=("Arial", 14, "bold")).pack(pady=10)
        
        tk.Label(dialog, text="Name:").pack()
        name_entry = tk.Entry(dialog, width=30)
        name_entry.insert(0, current_meal.name)
        name_entry.pack()
        
        tk.Label(dialog, text="Ingredients (comma separated):").pack()
        ing_entry = tk.Entry(dialog, width=30)
        ing_entry.insert(0, ", ".join(current_meal.ingredients))
        ing_entry.pack()
        
        tk.Label(dialog, text="Calories:").pack()
        cal_entry = tk.Entry(dialog, width=30)
        cal_entry.insert(0, str(current_meal.calories))
        cal_entry.pack()
        
        tk.Label(dialog, text="Category:").pack()
        category_var = tk.StringVar(value=current_meal.category)
        tk.OptionMenu(dialog, category_var, "Breakfast", "Lunch", "Dinner", "Snack").pack()
        
        def save_edit():
            name = name_entry.get().strip()
            ingredients = [i.strip() for i in ing_entry.get().split(",")]
            calories = cal_entry.get().strip()
            category = category_var.get()
            
            if not name:
                messagebox.showerror("Error", "Name cannot be empty!")
                return
            
            if not calories.isdigit() or int(calories) > 5000:
                messagebox.showerror("Error", "Calories must be a number of ≤5000!")
                return
            
            if edit_meal(meal_index, name, ingredients, int(calories), category):
                self.refresh_meal_table()
                dialog.destroy()
                messagebox.showinfo("Success", "Meal updated successfully!")
            else:
                messagebox.showerror("Error", "A meal with this name already exists!")
        
        def cancel_edit():
            dialog.destroy()
        
        button_frame = tk.Frame(dialog)
        button_frame.pack(pady=20)
        
        tk.Button(button_frame, text="Save Changes", command=save_edit, bg="green", fg="white", width=15).pack(side=tk.LEFT, padx=10)
        tk.Button(button_frame, text="Cancel", command=cancel_edit, bg="gray", fg="white", width=15).pack(side=tk.LEFT, padx=10)


    def meal_delete_dialog(self):
        selection = self.meal_tree.selection()
        
        if not selection:
            messagebox.showerror("Error", "Please select a meal to delete!")
            return
        
        selected_item = self.meal_tree.item(selection[0])
        values = selected_item['values']
        meal_index = values[0] - 1  
        
        meal = meals_list[meal_index]
        
        if messagebox.askyesno("Confirm", f"Delete '{meal.name}'?"):
            delete_meal(meal.name)
            self.refresh_meal_table()
            messagebox.showinfo("Success", "Meal deleted!")


    def meal_save(self):
        save_meals()
        messagebox.showinfo("Success", "Meals saved successfully!")

    # CHORE SCHEDULE SCREEN 
    def show_chore_screen(self):
        self.clear_window()
        
        header_frame = tk.Frame(self.root, bg="orange")
        header_frame.pack(fill=tk.X)
        tk.Label(header_frame, text="🧹 Chore Scheduler", font=("Arial", 18, "bold"), 
                bg="orange", fg="white", pady=10).pack()
        
        button_frame = tk.Frame(self.root)
        button_frame.pack(pady=10)
        
        tk.Button(button_frame, text="Add Chore", command=self.chore_add_dialog, bg="green", fg="white", width=15).pack(side=tk.LEFT, padx=5)
        tk.Button(button_frame, text="Edit Chore", command=self.chore_edit_dialog, bg="orange", fg="white", width=15).pack(side=tk.LEFT, padx=5)
        tk.Button(button_frame, text="Mark Complete", command=self.chore_mark_complete, bg="blue", fg="white", width=15).pack(side=tk.LEFT, padx=5)
        tk.Button(button_frame, text="Delete Chore", command=self.chore_delete_dialog, bg="red", fg="white", width=15).pack(side=tk.LEFT, padx=5)
        tk.Button(button_frame, text="Save", command=self.chore_save, bg="purple", fg="white", width=15).pack(side=tk.LEFT, padx=5)
        tk.Button(button_frame, text="Back", command=self.show_main_menu, bg="gray", fg="white", width=15).pack(side=tk.LEFT, padx=5)
        
        table_frame = tk.Frame(self.root)
        table_frame.pack(fill=tk.BOTH, expand=True, padx=10, pady=10)
        
        scrollbar_y = tk.Scrollbar(table_frame)
        scrollbar_y.pack(side=tk.RIGHT, fill=tk.Y)
        
        scrollbar_x = tk.Scrollbar(table_frame, orient=tk.HORIZONTAL)
        scrollbar_x.pack(side=tk.BOTTOM, fill=tk.X)
        
        self.chore_tree = ttk.Treeview(table_frame, columns=("ID", "Task", "Assignee", "Due Date", "Status"), 
                                      show="headings", yscrollcommand=scrollbar_y.set, 
                                      xscrollcommand=scrollbar_x.set, height=15)
        
        self.chore_tree.heading("ID", text="ID")
        self.chore_tree.heading("Task", text="Task")
        self.chore_tree.heading("Assignee", text="Assignee")
        self.chore_tree.heading("Due Date", text="Due Date")
        self.chore_tree.heading("Status", text="Status")
        
        self.chore_tree.column("ID", width=50, anchor=tk.CENTER)
        self.chore_tree.column("Task", width=200, anchor=tk.CENTER)  
        self.chore_tree.column("Assignee", width=100, anchor=tk.CENTER)
        self.chore_tree.column("Due Date", width=100, anchor=tk.CENTER)
        self.chore_tree.column("Status", width=100, anchor=tk.CENTER)
        
        self.chore_tree.pack(fill=tk.BOTH, expand=True)
        scrollbar_y.config(command=self.chore_tree.yview)
        scrollbar_x.config(command=self.chore_tree.xview)
        
        summary_frame = tk.Frame(self.root)
        summary_frame.pack(fill=tk.X, padx=10, pady=5)
        self.chore_summary_label = tk.Label(summary_frame, text="", font=("Arial", 10, "bold"))
        self.chore_summary_label.pack()
        
        self.refresh_chore_table()

    def refresh_chore_table(self):
        for item in self.chore_tree.get_children():
            self.chore_tree.delete(item)
        
        for chore in get_chores():
            self.chore_tree.insert("", tk.END, values=(chore.id, chore.task, chore.assignee, chore.due_date, chore.status))
        
        total_chores = len(chores_list)
        completed = sum(1 for c in chores_list if c.status == "✅Completed")
        pending = total_chores - completed
        
        stats_text = f"📊 Total Chores: {total_chores} | ✅ Completed: {completed} | ❗ Pending: {pending}"
        self.chore_summary_label.config(text=stats_text)

    def chore_add_dialog(self):
        dialog = tk.Toplevel(self.root)
        dialog.title("Add Chore")
        dialog.geometry("300x300")
        dialog.transient(self.root)
        dialog.grab_set()
        
        tk.Label(dialog, text="Add Chore", font=("Arial", 14, "bold")).pack(pady=10)
        
        tk.Label(dialog, text="Task:").pack(pady=5)
        task_entry = tk.Entry(dialog, width=30)
        task_entry.pack(pady=5)
        
        tk.Label(dialog, text="Assignee:").pack(pady=5)
        assignee_entry = tk.Entry(dialog, width=30)
        assignee_entry.pack(pady=5)
        
        tk.Label(dialog, text="Due Date (DD-MM-YYYY):").pack(pady=5)
        date_entry = tk.Entry(dialog, width=30)
        date_entry.insert(0, datetime.date.today().strftime("%d-%m-%Y"))
        date_entry.pack(pady=5)

        def add_chore_local():
            task = task_entry.get().strip()
            assignee = assignee_entry.get().strip()
            due_date = date_entry.get().strip()
            
            if not task or not assignee: 
                messagebox.showerror("Error", "Please fill the empty fields!")
                return
            
            if not assignee.replace(" ", "").isalpha():
                messagebox.showerror("Error", "Assignee must contain only letters and spaces!")
                return
            
            try: 
                datetime.datetime.strptime(due_date, "%d-%m-%Y")
            except ValueError: 
                messagebox.showerror("Error", "This is an invalid date format! Use format DD-MM-YYYY")
                return
            
            add_chore(task, assignee, due_date)
            self.refresh_chore_table()
            dialog.destroy()
            messagebox.showinfo("Success", "Chore added!")
        
        tk.Button(dialog, text="Add Chore", command=add_chore_local, width=20, bg="green", fg="white").pack(pady=10)

    def chore_edit_dialog(self):
        """Dialog to edit an existing chore"""
        selection = self.chore_tree.selection()
        
        if not selection:
            messagebox.showerror("Error", "Please select a chore to edit!")
            return
        
        selected_item = self.chore_tree.item(selection[0])
        values = selected_item['values']
        chore_id = values[0]  
        chore = None
        for c in chores_list:
            if c.id == chore_id:
                chore = c
                break
        
        if not chore:
            messagebox.showerror("Error", "Chore not found!")
            return
        
        if chore.status == "✅Completed":
            messagebox.showerror("Error", "Cannot edit a completed chore!")
            return
        
        dialog = tk.Toplevel(self.root)
        dialog.title("Edit Chore")
        dialog.geometry("300x300")
        dialog.transient(self.root)
        dialog.grab_set()
        
        tk.Label(dialog, text="Edit Chore", font=("Arial", 14, "bold")).pack(pady=10)
        
        tk.Label(dialog, text="Task:").pack(pady=5)
        task_entry = tk.Entry(dialog, width=30)
        task_entry.insert(0, chore.task)
        task_entry.pack(pady=5)
        
        tk.Label(dialog, text="Assignee:").pack(pady=5)
        assignee_entry = tk.Entry(dialog, width=30)
        assignee_entry.insert(0, chore.assignee)
        assignee_entry.pack(pady=5)
        
        tk.Label(dialog, text="Due Date (DD-MM-YYYY):").pack(pady=5)
        date_entry = tk.Entry(dialog, width=30)
        date_entry.insert(0, chore.due_date)
        date_entry.pack(pady=5)
        
        def save_edit():
            task = task_entry.get().strip()
            assignee = assignee_entry.get().strip()
            due_date = date_entry.get().strip()
            
            if not task or not assignee:
                messagebox.showerror("Error", "Please fill all fields!")
                return
            
            if not assignee.replace(" ", "").isalpha():
                messagebox.showerror("Error", "Assignee must contain only letters and spaces!")
                return
            
            try:
                datetime.datetime.strptime(due_date, "%d-%m-%Y")
            except ValueError:
                messagebox.showerror("Error", "Invalid date format! Use DD-MM-YYYY")
                return
            
            if edit_chore(chore.id, task, assignee, due_date):
                self.refresh_chore_table()
                dialog.destroy()
                messagebox.showinfo("Success", "Chore updated successfully!")
            else:
                messagebox.showerror("Error", "Failed to update chore!")
        
        def cancel_edit():
            dialog.destroy()
        
        button_frame = tk.Frame(dialog)
        button_frame.pack(pady=20)
        
        tk.Button(button_frame, text="Save Changes", command=save_edit, bg="green", fg="white", width=15).pack(side=tk.LEFT, padx=10)
        tk.Button(button_frame, text="Cancel", command=cancel_edit, bg="gray", fg="white", width=15).pack(side=tk.LEFT, padx=10)

    def chore_mark_complete(self):
        selection = self.chore_tree.selection()
        
        if not selection:
            messagebox.showerror("Error", "Please select a chore!")
            return
        
        selected_item = self.chore_tree.item(selection[0])
        values = selected_item['values']
        chore_id = values[0]  
        
        if mark_chore_complete(chore_id):
            self.refresh_chore_table()
            messagebox.showinfo("Success", "You have completed the chore!")
        else:
            messagebox.showerror("Error", "Failed to mark chore as complete!")

    def chore_delete_dialog(self):
        selection = self.chore_tree.selection()
        
        if not selection:
            messagebox.showerror("Error", "Please select a chore to delete!")
            return
        
        selected_item = self.chore_tree.item(selection[0])
        values = selected_item['values']
        chore_id = values[0] 
        
        if messagebox.askyesno("Confirm", "Delete this chore?"):
            delete_chore(chore_id)
            self.refresh_chore_table()
            messagebox.showinfo("Success", "Chore deleted!")

    def chore_save(self):
        try: 
            save_chores()
            messagebox.showinfo("Success", "Chores saved successfully!")
        except: 
            messagebox.showerror("Error", "Failed to save chores!")

if __name__ == "__main__":
    root = tk.Tk()
    app = SimpleHomeManager(root)
    root.mainloop()