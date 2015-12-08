#ifndef LINKED_LISTS_H
#define LINKED_LISTS_H
#include <iostream>
#include <map>

template <typename T>
class LinkedList {

private:
    class Node {
        friend class LinkedList<T>;

    private:
        T data;
        Node* next;

    public:
        Node(T d, Node* n = NULL) : data(d), next(n) {}
		void log(){	std::cout<<data<<std::endl; }

	};

    Node *head,*tail;  // End of list
    int size;    // Number of nodes in list

public:

    LinkedList(const LinkedList<T>& src);  // Copy constructor
    ~LinkedList();  // Destructor

    LinkedList() : head(NULL), tail(NULL), size(0) {}

    // Returns a reference to first element
    T& front() {
        return head->data;
    }

    // Returns a reference to last element
    T& back() {
        return tail->data;
    }

	LinkedList<int>& operator+(LinkedList<int> &other );
    LinkedList<T>& push_front(T);  // Insert element at beginning
    LinkedList<T>& push_back(T);   // Insert element at end
    LinkedList<T>& pop_front();  // Remove element from beginning
    LinkedList<T>& pop_back();  // Remove element from end
	LinkedList<T>& remove_duplicates();
	Node* findByIndex(int i);
	LinkedList<T>& push_partitioned(const T& d,const T& checked,bool (*append_after)(T, T));
    void dump();  // Output contents of list
};

// Copy constructor
template <typename T>
LinkedList<T>::LinkedList(const LinkedList<T>& src) :
        head(NULL), tail(NULL), size(0) {

    Node* current = src.head;
    while (current != NULL) 
	{
        this->push_back(current->data);
        current = current->next;
    }

}

// Destructor
template <typename T>
LinkedList<T>::~LinkedList() 
{
    while (size!=0) 
		this->pop_front();   
}

// Insert an element at the beginning
template <typename T>
LinkedList<T>& LinkedList<T>::push_front(T d) {

    Node* new_head = new Node(d, head);

    if (size==0) 
        tail = new_head;
    head = new_head;
    ++size;
	return *this;
}

// Insert an element at the end
template <typename T>
LinkedList<T>& LinkedList<T>::push_back(T d) {

    Node* new_tail = new Node(d, NULL);

    if (size==0) 
        head = new_tail;
     else 
        tail->next = new_tail;

    tail = new_tail;
    ++size;
	return *this;
}

// Remove an element from the beginning
template <typename T>
LinkedList<T>& LinkedList<T>::pop_front() 
{
    Node* old_head = head;

    if (size==1) 
	{
        head = NULL;
        tail = NULL;
    } 
	else 
        head = head->next;
    
    delete old_head;
    --size;
	return *this;
}
 
template <typename T>
LinkedList<T>& LinkedList<T>::pop_back() 
{
    Node* old_tail = tail;

    if (size== 1) 
	{
        head = NULL;
        tail = NULL;
    } 
	else 
	{
        // Traverse the list to node just before tail
        Node* current = head;
        while (current->next != tail) 
            current = current->next;

        // Unlink and reposition
        current->next = NULL;
        tail = current;
    }

    delete old_tail;
    --size;
	return *this;
}

/*
Write code to remove duplicates from an unsorted linked list.
How would you solve this problem if a temporary buffer is not allowed?
*/
template <typename T>
LinkedList<T>& LinkedList<T>::remove_duplicates() 
{
	Node* current = head;
	std::map<T,bool> repeats; 
    if (current != NULL) 
	{
		while (current->next != NULL) 
		{
			Node* n = current->next;
			Node* prev = current;
				while(n != NULL){
					if(current->data == n->data)
					{
						Node *removed = n;
						n = n->next;
						prev->next = n;
						--size;
						delete removed;
					} 
					else
					{ 
						prev = prev->next;
						n = n->next;
					}

				}				
            current = current->next;
        }
	}
	return *this;
}


// Display the contents of the list
template <typename T>
void LinkedList<T>::dump() {

    std::cout << "(";

    Node* current = head;

    if (current != NULL) 
        while (current->next != NULL) 
		{
            std::cout << current->data << ", ";
            current = current->next;
        }
        std::cout << current->data;

    std::cout << ")" << std::endl;
}


template <typename T>
typename LinkedList<T>::Node* LinkedList<T>::findByIndex(int i) {
	int counter = 0;
	Node* current = head;
	if (current != NULL) 
		for (;current->next != NULL;current=current->next) 
		{
			if(counter == i) return current;
			++counter;
		}
 return nullptr;
}

/*
Write code to partition a linked list around a value x, such that all nodes less than
x come before all nodes greater than or equal to x.
*/

template <typename T>
LinkedList<T>& LinkedList<T>::push_partitioned(const T& d,const T& checked,bool (*append_after)(T, T))
{
	if(append_after(d,checked))push_back(d);
	else  push_front(d);
	return *this;
}

/*
You have two numbers represented by a linked list, where each node contains a
single digit. The digits are stored in reverse order, such that the Ts digit is at the
head of the list. Write a function that adds the two numbers and returns the sum
as a linked list.
EXAMPLE
Input: (7-> 1  -> 6) + (5 -> 9 -> 2).That is, 617 + 295.
Output: 2 -> 1  -> 9.That is, 912.

*/

template <>
LinkedList<int>& LinkedList<int>::operator+(LinkedList<int>& other)
{
	Node* current = head;
	Node* other_current = other.head;
	
	if (current != NULL && other_current != NULL) 
	{
	int carry = 0;
	for (;current != NULL,other_current !=NULL;current=current->next, other_current = other_current->next) 
		{
			int curr_data = current->data;	
			int other_curr_data = other_current->data;
			int sum =  curr_data + other_curr_data + carry;
			int mod = sum % 10;
			carry =  sum >= 10 ? 1 : 0;
			current->data = mod;
		}
	}
	return *this;
}

#endif