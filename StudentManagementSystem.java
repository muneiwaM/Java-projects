/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Main.java to edit this template
 */
package studentmanagementsystem;

import java.util.Scanner;

/**
 *
 * @author munei
 */
public class StudentManagementSystem {

    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
      try (Scanner sc = new Scanner(System.in)) {
            StudentManager manager = new StudentManager();
            int choice;
            
            do {
                System.out.println("\n1. Add Student");
                System.out.println("2. Display Students");
                System.out.println("3. Search Student");
                System.out.println("4. Remove Student");
                System.out.println("5. Update Student");
                System.out.println("6. Exit");
                System.out.print("Enter your choice: ");
                while (!sc.hasNextInt()) {
                    System.out.println("Invalid input! Please enter a number between 1 and 6.");
                    sc.next();
                }
                choice = sc.nextInt();
                sc.nextLine(); // consume newline
                
                switch (choice) {
                    case 1 -> {
                        System.out.print("Enter Student ID: ");
                        String id = sc.nextLine();
                        System.out.print("Enter Name: ");
                        String name = sc.nextLine();
                        int age;
                        while (true) {
                            System.out.print("Enter Age: ");
                            if (sc.hasNextInt()) {
                                age = sc.nextInt();
                                sc.nextLine();
                                if (age > 0) break;
                                else System.out.println("Age must be positive!");
                            } else {
                                System.out.println("Invalid input! Please enter a valid age.");
                                sc.next();
                            }
                        }
                        manager.addStudent(new Student(id, name, age));
                    }
                        
                    case 2 -> manager.displayStudents();
                        
                    case 3 -> {
                        System.out.print("Enter Student ID to search: ");
                        String searchId = sc.nextLine();
                        Student s = manager.searchStudent(searchId);
                        if (s != null) {
                            System.out.println("Student found: " + s);
                        } else {
                            System.out.println("Student not found.");
                        }
                    }
                        
                    case 4 -> {
                        System.out.print("Enter Student ID to remove: ");
                        String removeId = sc.nextLine();
                        if (manager.removeStudent(removeId)) {
                            System.out.println("Student removed successfully!");
                        } else {
                            System.out.println("Student not found.");
                        }
                    }
                        
                    case 5 -> {
                        System.out.print("Enter Student ID to update: ");
                        String updateId = sc.nextLine();
                        Student stu = manager.searchStudent(updateId);
                        if (stu != null) {
                            System.out.print("Enter new name: ");
                            String newName = sc.nextLine();
                            int newAge;
                            while (true) {
                                System.out.print("Enter new age: ");
                                if (sc.hasNextInt()) {
                                    newAge = sc.nextInt();
                                    sc.nextLine();
                                    if (newAge > 0) break;
                                    else System.out.println("Age must be positive!");
                                } else {
                                    System.out.println("Invalid input! Please enter a valid age.");
                                    sc.next();
                                }
                            }
                            manager.updateStudent(updateId, newName, newAge);
                            System.out.println("Student updated successfully!");
                        } else {
                            System.out.println("Student not found.");
                        }
                    }
                        
                    case 6 -> System.out.println("Exiting... Goodbye!");
                        
                    default -> System.out.println("Invalid choice! Please enter a number between 1 and 6.");
                }
            } while (choice != 6);
        }
    }
}
   

